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

    public function __construct($reservation_id = false)
    {

        if($reservation_id){
            $this->reservation_id = $reservation_id;

            $this->reservation = Reservation::findOrFail($this->reservation_id);
        }
    }

    public function fiskalReservation(){

        $error = false;

        $invoice_type = 'reservation';
        #Get End Location
        $owner_location = false;

        $reservation = $this->reservation;

        #Avoid Sending Invoice Cancellation
        if($reservation->getOverallReservationStatus() == 'cancelled'){
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

            $owner_location = $this->getOwnerLocation($acc_opera_code,$acc_opera_class);

            if($owner_location){

                #Set Property Code
                $this->dropoffLocationPMSCode = $owner_location->pms_code;

                #Get Owner - Fetch OIB
                $owner = Owner::findOrFail($owner_location->owner_id);

                if($owner->oib){

                    $this->oib = $owner->oib;


                    $price = $this->reservation->price;

                    if($this->reservation->isRoundTrip && $this->reservation->returnReservation->status == 'confirmed'){
                        $price = $price*2;
                    }

                    if($this->reservation->isRoundTrip && $this->reservation->status == 'cancelled' && $this->reservation->returnReservation->status == 'confirmed'){
                        $price = $this->reservation->returnReservation->price;
                    }


                    $zki =   Fiskal::GenerateZKI(
                        Carbon::now(),
                        $owner->oib,
                        1,
                        01,
                        02,
                        number_format($price/100,2,'.',''),
                        $owner_location);

                    $next_invoice = ($owner_location->fiskal_invoice_no+1);

                    if(strlen($next_invoice) == 1){
                        $next_invoice = '0'.$next_invoice;
                    }

                    if($reservation->included_in_accommodation_reservation == 1 || $reservation->v_level_reservation){
                        #Fiskal - send to 0
                        $price = 0;
                    }

                    $amount = number_format($price/100,2,'.','');

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
        }else{
                $error = 'Unable to find Property with PropertyCode: '.$acc_opera_code.' and PropertyClass: '.$acc_opera_class;
            }

        }else{
            $error = 'Unable to pull the reservation data for reservation: '.$reservation_code;
        }

        #Write a fiskal Error
        if($error){
            ValamarFiskalizacija::write_db_log(
                $this->reservation_id,
                'fiskal',
                'Reservation ID: '.$this->reservation_id,
                json_encode(array('error' => $error)),
                '',
                '',
                self::STATUS_ERROR
            );
        }

    }

    private function getOwnerLocation($acc_opera_code,$acc_opera_class){

        $owner_location = false;

        $owner_location = Point::where('pms_code','=',$acc_opera_code)
            ->where('pms_class','=',$acc_opera_class)->get()->first();

        if(!$owner_location){
            $owner_location = Point::where('pms_code','=',$acc_opera_code)->get()->first();
        }

        if(empty($owner_location)){

            $points = Point::all();
            $candidates = array();

            if(!empty($points)){
                foreach($points as $point){

                    $p_codes = explode(',',$point->pms_code);

                    if(in_array($acc_opera_code,$p_codes)){
                        $candidates[] = $point;
                    }
                }

                if(!empty($candidates)){

                    if(count($candidates) == 1){
                        $owner_location = $candidates[0];
                    }else{
                        #Check if both code and class are  existent
                        foreach($candidates as $candidate){
                            if($candidate->pms_class == $acc_opera_class){
                                $owner_location = $candidate;
                            }
                        }
                    }



                }
            }

        }

        return $owner_location;
    }

    public function syncDocument(){

        if(config('valamar.valamar_opera_fiskalizacija_active')) {


            $error = false;

            $invoice_type = 'reservation';
            #Get End Location
            $owner_location = false;

            $reservation = $this->reservation;

            #Avoid Sending Invoice Cancellation
            if ($reservation->getOverallReservationStatus() == 'cancelled') {
               return 'Cannot Sync Document - Reservation Already Cancelled';
            }

            $traveller_info = $reservation->getLeadTravellerAttribute();

            $reservation_code = $traveller_info->reservation_number;

            $valamar_api = new ValamarClientApi();

            $valamar_api->setReservationCodeFilter($reservation_code);

            $opera_res_data = $valamar_api->getReservationList();


            #Fetch the reservation from Opera
            if (!empty($opera_res_data[$reservation_code])) {

                $acc_opera_code = $opera_res_data[$reservation_code]['propertyOperaCode'];
                $acc_opera_class = $opera_res_data[$reservation_code]['propertyOperaClass'];

                $owner_location = Point::where('pms_code', '=', $acc_opera_code)
                    ->where('pms_class', '=', $acc_opera_class)->get()->first();


                if (!$owner_location) {
                    $owner_location = Point::where('pms_code', '=', $acc_opera_code)->get()->first();
                }

                if ($owner_location) {

                    $owner = Owner::findOrFail($owner_location->owner_id);
                    $this->oib = $owner->oib;
                    #Set Property Code
                    $this->dropoffLocationPMSCode = $owner_location->pms_code;

                    $this->setAuthenticationHeaders();

                    if ($this->validateReservationNumber() && $this->validatePMSCode()) {
                        $this->buildRequestStruct();
                        $this->sendOperaRequest();
                    }
                }
            }
        }
    }

    public function fiskalReservationCF($cancellation_fee){

        #Get End Location
        $owner_location = false;

        $reservation = $this->reservation;

        $traveller_info = $reservation->getLeadTravellerAttribute();

        $reservation_code = $traveller_info->reservation_number;

        $valamar_api = new ValamarClientApi();
        $valamar_api->setReservationCodeFilter($reservation_code);

        $opera_res_data = $valamar_api->getReservationList();


        #Fetch the reservation from Opera
        if(!empty($opera_res_data[$reservation_code])){

            $acc_opera_code = $opera_res_data[$reservation_code]['propertyOperaCode'];
            $acc_opera_class = $opera_res_data[$reservation_code]['propertyOperaClass'];

            $owner_location = $this->getOwnerLocation($acc_opera_code,$acc_opera_class);

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
                        number_format($cancellation_fee,2,'.',''),$owner_location);


                    $next_invoice = ($owner_location->fiskal_invoice_no+1);

                    if(strlen($next_invoice) == 1){
                        $next_invoice = '0'.$next_invoice;
                    }

                    $amount = number_format($cancellation_fee,2,'.','');

                    if($this->reservation->cf_null == 1){
                        $amount = 0;
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
                            $invoice->invoice_type = self::INVOICE_TYPE_CANCELLATION_FEE;
                            $invoice->amount = $amount;


                            if(!empty($response['log_id'])){
                                $invoice->log_id = $response['log_id'];
                            }else{
                                $invoice->log_id = 0;
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
        $this->request['ZKI'] = $this->zki == null ? $this->reservation->getInvoiceData('zki') : $this->zki;
        #JIR
        $this->request['JIR'] = $this->jir == null ? $this->reservation->getInvoiceData('jir') : $this->jir;
        #DocumentID
        if($this->reservation->getInvoiceData('invoice_number') != '-'){
            $this->request['DocumentID'] = $this->reservation->getInvoiceData('invoice_number');
        }else{
            $this->request['DocumentID'] = $this->invoice->invoice_id.'/'.$this->invoice->invoice_establishment.'/'.$this->invoice->invoice_device;
        }

        #OIB
        $this->request['OIB'] = trim($this->oib);
        #Total
        $this->request['Total'] = $this->amount == null ? $this->reservation->getDisplayPrice()->formatByDecimal() : $this->amount;
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

                $this->reservation->connected_document_sync = 0;
                $this->reservation->save();

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

                $this->reservation->connected_document_sync = 0;
                $this->reservation->save();
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

                $this->reservation->connected_document_sync = 0;
                $this->reservation->save();

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

                $this->reservation->connected_document_sync = 1;
                $this->reservation->save();

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



    public function validatePackageIDMapping(array $packageIDs){


        if(!empty($packageIDs)){

            $this->setAuthenticationHeaders();
            $this->setCallURL('CheckPackages');

            #Auth Credentials
            $this->request = $this->auth_credentials;

            #PMS Reservation ID

            $this->request['PackageIDList'] = $packageIDs;

            $response = Http::post($this->callURL,$this->request);

            if(!$response->successful()){

            }else{
                $this->responseBody = $response->json();
            }

        }

        return $this->responseBody;

    }

}
