<?php

namespace App\Services\Api;

use App\Models\Invoice;
use App\Models\Owner;
use App\Models\Point;
use App\Models\Reservation;
use App\Models\Traveller;
use App\Services\Fiskal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use function Symfony\Component\String\b;
use Exchanger\Exception\Exception;
use Illuminate\Support\Facades\Http;

class ValamarFiskalizacija{

    private array $auth_credentials = array();
    private array $request;
    private array $authHeaders;
    private string $callURL;
    private array $responseBody;
    private string $apiDateFormat = 'Y-m-d';
    private int $reservation_id;
    private Reservation $reservation;
    private string $dropoffLocationPMSCode;
    private $zki;
    private $jir;
    private $invoice;
    private $amount;
    private $oib;

    const LOG_TYPE_ARRAY = array(
      'fiskal',
      'opera'
    );

    const INVOICE_TYPE_RESERVATION = 'reservation';
    const INVOICE_TYPE_CANCELLATION = 'cancellation';
    const INVOICE_TYPE_CANCELLATION_FEE = 'cancellation_fee';

    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';

    CONST STATUS_ARRAY = array(
        'success',
        'error'
    );

    public function __construct($reservation_id)
    {

        $this->reservation_id = $reservation_id;

        $this->reservation = Reservation::findOrFail($this->reservation_id);

    }

    public function fiskalReservation(){


        $invoice_type = 'reservation';
        #Get End Location
        $owner_location = false;

        $reservation = $this->reservation;

        if($reservation->included_in_accommodation_reservation == 1){
            return true;
        }

        $traveller_info = $reservation->getLeadTravellerAttribute();

        $reservation_code = $traveller_info->reservation_number;

        $valamar_api = new ValamarClientApi();
        $valamar_api->setReservationCodeFilter($reservation_code);

        $opera_res_data = $valamar_api->getReservationList();

        #Fetch the reservation from Opera
        if(!empty($opera_res_data[$reservation_code])){

            $acc_opera_code = $opera_res_data[$reservation_code]['propertyOperaCode'];
            $acc_opera_class = $opera_res_data[$reservation_code]['propertyOperaClass'];

            $owner_location = Point::where('pms_code','=',$acc_opera_code)
                                    ->where('pms_class','=',$acc_opera_class)->get()->first();

            if(!$owner_location){
                $owner_location = Point::where('pms_code','=',$acc_opera_code)->get()->first();
            }

            if($owner_location){

                #Set Property Code
                $this->dropoffLocationPMSCode = $owner_location->pms_code;

                #Get Owner - Fetch OIB
                $owner = Owner::findOrFail($owner_location->owner_id);

                if($owner->oib){

                    $this->oib = $owner->oib;

                    $zki =   Fiskal::GenerateZKI(
                        Carbon::now(),
                        $owner->oib,
                        1,
                        01,
                        02,
                        number_format($this->reservation->price/100,2),$owner_location);

                    $next_invoice = ($owner_location->fiskal_invoice_no+1);

                    if(strlen($next_invoice) == 1){
                        $next_invoice = '0'.$next_invoice;
                    }

                    $amount = number_format($this->reservation->price/100,2);

                    if($reservation->status == Reservation::STATUS_CANCELLED){
                        $amount = '-'.$amount;
                        $invoice_type = 'cancellation';
                    }

                    $this->amount = $amount;

                    $response = Fiskal::Fiskal(
                        $this->reservation_id,
                        Carbon::now(),
                        $owner->oib,
                        $next_invoice,
                        $owner_location->fiskal_establishment,
                        $owner_location->fiskal_device,
                        $this->amount,
                        $zki,
                        false,
                        $owner_location
                    );


                    if(!empty($response)){

                        if($response['success'] == true && !empty($response['jir'])){

                            $this->zki = $zki;
                            $this->jir = $response['jir'];

                            #Update Invoice Order Number
                            $owner_location->fiskal_invoice_no = $next_invoice;
                            $owner_location->save();

                            $invoice = new Invoice();
                            $invoice->reservation_id = $this->reservation_id;
                            $invoice->zki = $this->zki;
                            $invoice->jir = $this->jir;
                            $invoice->invoice_id = $next_invoice;
                            $invoice->invoice_establishment = $owner_location->fiskal_establishment;
                            $invoice->invoice_device = $owner_location->fiskal_device;
                            $invoice->amount = $amount;
                            $invoice->log_id = 0;
                            $invoice->invoice_type = $invoice_type;

                            if(!empty($response['log_id'])){
                                $invoice->log_id = $response['log_id'];
                            }

                            $invoice->save();

                            $this->invoice = $invoice;

                            if(config('valamar.valamar_opera_fiskalizacija_active')){
                                $this->setAuthenticationHeaders();
                                if($this->validateReservationNumber() && $this->validatePMSCode()){
                                    $this->buildRequestStruct();
                                    $this->sendOperaRequest();
                                }
                            }
                        }

                    }
            }
        }

        }

    }

    public function fiskalReservationCF($cancellation_fee){

        #Get End Location
        $owner_location = false;

        $reservation = $this->reservation;

        if($reservation->included_in_accommodation_reservation == 1){
            return true;
        }

        $traveller_info = $reservation->getLeadTravellerAttribute();

        $reservation_code = $traveller_info->reservation_number;

        $valamar_api = new ValamarClientApi();
        $valamar_api->setReservationCodeFilter($reservation_code);

        $opera_res_data = $valamar_api->getReservationList();

        #Fetch the reservation from Opera
        if(!empty($opera_res_data[$reservation_code])){

            $acc_opera_code = $opera_res_data[$reservation_code]['propertyOperaCode'];
            $acc_opera_class = $opera_res_data[$reservation_code]['propertyOperaClass'];

            $owner_location = Point::where('pms_code','=',$acc_opera_code)
                                    ->where('pms_class','=',$acc_opera_class)->get()->first();

            if(!$owner_location){
                $owner_location = Point::where('pms_code','=',$acc_opera_code)->get()->first();
            }

            if($owner_location){

                #Set Property Code
                $this->dropoffLocationPMSCode = $owner_location->pms_code;

                #Get Owner - Fetch OIB
                $owner = Owner::findOrFail($owner_location->owner_id);

                if($owner->oib){

                    $this->oib = $owner->oib;

                    $zki =   Fiskal::GenerateZKI(
                        Carbon::now(),
                        $owner->oib,
                        1,
                        01,
                        02,
                        number_format($cancellation_fee,2),$owner_location);


                    $next_invoice = ($owner_location->fiskal_invoice_no+1);

                    if(strlen($next_invoice) == 1){
                        $next_invoice = '0'.$next_invoice;
                    }

                    $amount = number_format($cancellation_fee,2);

                    $this->amount = $amount;

                    $response = Fiskal::Fiskal(
                        $this->reservation_id,
                        Carbon::now(),
                        $owner->oib,
                        $next_invoice,
                        $owner_location->fiskal_establishment,
                        $owner_location->fiskal_device,
                        $this->amount,
                        $zki,
                        false,
                        $owner_location
                    );


                    if(!empty($response)){

                        if($response['success'] == true && !empty($response['jir'])){

                            $this->zki = $zki;
                            $this->jir = $response['jir'];

                            #Update Invoice Order Number
                            $owner_location->fiskal_invoice_no = $next_invoice;
                            $owner_location->save();

                            $invoice = new Invoice();
                            $invoice->reservation_id = $this->reservation_id;
                            $invoice->zki = $this->zki;
                            $invoice->jir = $this->jir;
                            $invoice->invoice_id = $next_invoice;
                            $invoice->invoice_establishment = $owner_location->fiskal_establishment;
                            $invoice->invoice_device = $owner_location->fiskal_device;
                            $invoice->invoice_type = self::INVOICE_TYPE_RESERVATION;
                            $invoice->amount = $amount;
                            $invoice->log_id = 0;
                            $invoice->invoice_type = 'cancellation_type';

                            $invoice->save();

                            $this->invoice = $invoice;

                            if(config('valamar.valamar_opera_fiskalizacija_active')){
                                $this->setAuthenticationHeaders();
                                if($this->validateReservationNumber() && $this->validatePMSCode()){
                                    $this->buildRequestStruct();
                                    $this->sendOperaRequest();
                                }
                            }
                        }

                    }
            }
        }

        }

    }

    /**
     * Function used to send request towards the API
     * @return void
     */
    private function sendOperaRequest() : bool{

        $this->setCallURL('FiskalizacijaPrateciDokument');

        $this->validateResponse(
            Http::post($this->callURL,$this->request));

        return false;
    }

    /**
     * Function used to prepare the request needed to be sent towards Opera API Interface
     * @return void
     */
    private function buildRequestStruct(): bool{

        #Auth Credentials
        $this->request = $this->auth_credentials;
        #Resort - Point Destination Code
        $this->request['Resort'] = $this->dropoffLocationPMSCode;
        #PMSReservationID - Reservation Number
        $this->request['PMSReservationID'] = $this->reservation_opera_id;
        #ZKI
        $this->request['ZKI'] = $this->zki;
        #JIR
        $this->request['JIR'] = $this->jir;
        #DocumentID
        $this->request['DocumentID'] = $this->invoice->invoice_id.'/'.$this->invoice->invoice_establishment.'/'.$this->invoice->invoice_device;
        #OIB
        $this->request['OIB'] = $this->oib;
        #Total
        $this->request['Total'] = $this->amount;
        #Timestamp
        $this->request['TimeStamp'] = Carbon::now()->toDateTimeString();

        return true;
    }


    private function setAuthenticationHeaders() : void{
        $this->auth_credentials = array(
            'SysUser' =>  config('valamar.valamar_opera_api_user'),
            'SysPass' => config('valamar.valamar_opera_api_pass')
        );
    }

    /**
     * @param \Illuminate\Http\Client\Response $response The Curl Response
     * @return ValamarOperaApi
     * @throws \Illuminate\Http\Client\RequestException Throw Exception with the appropriate error
     */
    private function validateResponse(\Illuminate\Http\Client\Response $response) : ValamarFiskalizacija
    {

        if(!$response->successful()){

            if($response->serverError()){

                ValamarFiskalizacija::write_db_log(
                    $this->reservation_id,
                    'opera',
                    json_encode($this->request),
                    json_encode(array('error' => $response->serverError())),
                    $this->zki,
                    $this->jir,
                    self::STATUS_ERROR
                );

                $response->throw($response->serverError());

            }else{
                ValamarFiskalizacija::write_db_log(
                    $this->reservation_id,
                    'opera',
                    json_encode($this->request),
                    json_encode(array('error' => $response->clientError())),
                    $this->zki,
                    $this->jir,
                    self::STATUS_ERROR
                );

                $response->throw($response->clientError());
            }

        }else{

            $this->responseBody = $response->json();

            if($this->responseBody['Status'] == 'ERR'){

                ValamarFiskalizacija::write_db_log(
                    $this->reservation_id,
                    'opera',
                    json_encode($this->request),
                    json_encode(array('error' => $response['ErrorList'])),
                    $this->zki,
                    $this->jir,
                    self::STATUS_ERROR
                );

            }else{
                ValamarFiskalizacija::write_db_log(
                    $this->reservation_id,
                    'opera',
                    json_encode($this->request),
                    json_encode($this->responseBody),
                    $this->zki,
                    $this->jir,
                    self::STATUS_SUCCESS
                );
            }
        }

        return $this;
    }

    /**
     * Function used to validate that the reservation number has been set for this reservation
     * @return bool True if the reservation number has been set for this reservation, false otherwise
     */
    private function validateReservationNumber() : bool{

        $return = false;

        $this->reservation->lead_traveller?->reservation_opera_id;

        if($this->reservation->lead_traveller?->reservation_opera_id != null){

            $this->reservation_opera_id = $this->reservation->lead_traveller?->reservation_opera_id;
            $return = true;
        }

        return $return;
    }

    /**
     * Function used to validate that the PMS code for the dropoff location has been set
     * @return bool True if the PMS code was entered for the Drop off Point ID, false otherwise
     */
    private function validatePMSCode() : bool{

        $return = false;

        if($this->dropoffLocationPMSCode != null){
            $return = true;
        }

        return $return;
    }
    /**
     * @param $res_id Write DB Log
     * @param $log_type fiskal | opera
     * @param $request_array array()
     * @param $response_array array()
     * @param $zki string | ''
     * @param $jir string | ''
     * @param $status success | error
     * @return void
     */
    static function write_db_log($res_id,$log_type,$request,$response,$zki = '',$jir = '',$status){

        $log_id = DB::table('opera_fiskal_log')->insertGetId(
          array('reservation_id' => $res_id,'log_type'=>$log_type,'request'=>$request,'response'=>$response,'zki'=>$zki,'jir'=>$jir,'status'=>$status)
        );

        return $log_id;

    }

    /**
     * @param $method String - allowed methods PackagePosting
     * @return void
     */
    private function setCallURL($method) : void{
        $this->callURL = config('valamar.valamar_opera_api_url')."/".$method;
    }


}
