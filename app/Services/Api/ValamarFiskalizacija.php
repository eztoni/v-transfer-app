<?php

namespace App\Services\Api;

use App\Models\Owner;
use App\Models\Point;
use App\Models\Reservation;
use App\Models\Traveller;
use App\Services\Fiskal;
use Carbon\Carbon;
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

    const LOG_TYPE_ARRAY = array(
      'fiskal',
      'opera'
    );

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

        #Get End Location
        $owner_location = false;

        if($this->reservation->pickup_address_id > 0){
            $owner_location = Point::findOrFail($this->reservation->pickup_address_id);

            if($owner_location->type != 'accommodation'){
                $owner_location = Point::findOrFail($this->reservation->dropoff_address_id);
            }
        }else{
            $owner_location = Point::findOrFail($this->reservation->dropoff_address_id);
        }

        #Get Owner - Fetch OIB
        $owner = Owner::findOrFail($owner_location->owner_id);

        if($owner->oib){

            $zki =   Fiskal::GenerateZKI(
                Carbon::now(),
                $owner->oib,
                1,
                01,
                02,
                number_format($this->reservation->price/100,2),$owner_location);

            $response = Fiskal::Fiskal(
                $this->reservation_id,
                Carbon::now(),
                $owner->oib,
                1,
                01,
                01,
                number_format($this->reservation->price/100,2),
                $zki,
                false,
                $owner_location
            );

            if(!empty($response)){

                if($response['success'] == true && !empty($response['jir'])){

                    $this->zki = $zki;
                    $this->jir = $response['jir'];

                    $lead_traveller = $this->reservation->getLeadTravellerAttribute('id');
                    $lead_traveller->jir = $this->jir;
                    $lead_traveller->zki = $this->zki;

                    $lead_traveller->save();


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
        $this->request['DocumentID'] = '22/33/11';
        #OIB
        $this->request['OIB'] = '12421523465342';
        #Total
        $this->request['Total'] = number_format($this->reservation->price/100,2);
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

        $dropoff_location = Point::find($this->reservation->dropoff_location);

        if($dropoff_location->pms_code != null){

            $this->dropoffLocationPMSCode = $dropoff_location->pms_code;
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

        \DB::insert('insert into opera_fiskal_log (reservation_id, log_type ,request,response,zki, jir, status) values (?, ?, ?, ?, ?, ?,?)',
            [
                $res_id,
                $log_type,
                $request,
                $response,
                $zki,
                $jir,
                $status]
        );
    }

    /**
     * @param $method String - allowed methods PackagePosting
     * @return void
     */
    private function setCallURL($method) : void{
        $this->callURL = config('valamar.valamar_opera_api_url')."/".$method;
    }


}