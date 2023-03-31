<?php

namespace App\Services\Api;
use App\Models\Point;
use App\Models\Reservation;
use App\Models\Route;
use App\Models\Transfer;
use App\Models\Traveller;
use Carbon\Carbon;
use Exchanger\Exception\Exception;
use Illuminate\Support\Facades\Http;
use Cknow\Money\Money;
use Money\Currency;

class ValamarOperaApi{

    private array $auth_credentials = array();
    private Reservation $reservation;
    private string $reservation_opera_id = '';
    private string $resortPMSCode = '';
    private array $errors;
    private Traveller $lead_traveller;
    private array $request = array();
    private array $responseBody = array();
    private string $packageID;
    private string $callURL;
    private Route $route;

    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';

    CONST STATUS_ARRAY = array(
        'success',
        'error'
    );

    const VARIABLE_PACKAGE_PRICE = 'ARTICLE';

    function __construct(){
        $this->setAuthenticationHeaders();
    }

    public function syncReservationWithOpera($reservation_id){

        $this->reservation = Reservation::findOrFail($reservation_id);

        if($this->validateReservationMapping()){
            if($this->buildRequestStruct()){
                $this->sendOperaRequest();
            }
        }else{
            $this->request = $this->errors;
            $this->writeCommunicationLog(self::STATUS_ERROR);
        }
    }

    private function setAuthenticationHeaders() : void{
        $this->auth_credentials = array(
            'SysUser' =>  config('valamar.valamar_opera_api_user'),
            'SysPass' => config('valamar.valamar_opera_api_pass')
        );
    }

    /**
     * Function used to validate whether all the parameters have been set
     * @return bool Returns true if all the properties for the reqest have the valid mapping structure
     */
    private function validateReservationMapping() : bool{

        $return = true;

        #PMS Code Validation
        if(!$this->validatePMSCode()){
            $return = false;
            $this->errors[] = 'No Mapped PMS code for the destination';
        }

        #Reservation Number Validation
        if(!$this->validateReservationNumber()){
            $return = false;
            $this->errors[] = 'No reservation number for this reservation';
        }

        #Validate Package ID - Route PMS Code
        if(!$this->validatePackageID()){
            $return = false;
            $this->errors[] = 'Missing Route Opera Package ID';
        }

        return $return;
    }

    /**
     * Function used to validate that the PMS code for the dropoff location has been set
     * @return bool True if the PMS code was entered for the Drop off Point ID, false otherwise
     */
    private function validatePMSCode() : bool{

        $return = false;

        if($this->reservation->getLeadTravellerAttribute()->reservation_number){
           $api = new ValamarClientApi();
           $api->setReservationCodeFilter($this->reservation->getLeadTravellerAttribute()->reservation_number);

           $res_result = $api->getReservationList();

           if(!empty($res_result[$this->reservation->getLeadTravellerAttribute()->reservation_number])){
               $this->resortPMSCode = $res_result[$this->reservation->getLeadTravellerAttribute()->reservation_number]['propertyOperaCode'];
               return true;
           }
        }
        
        return $return;
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
     * Function used to validate that the reservation number has been set for this reservation
     * @return bool True if the reservation number has been set for this reservation, false otherwise
     */
    private function validatePackageID() : bool{

        $return = false;

        $this->route = Route::query()
            ->where('destination_id', $this->reservation->destination_id)
            ->where('starting_point_id', $this->reservation->pickup_location)
            ->where('ending_point_id', $this->reservation->dropoff_location)
            ->get()->first();


        if(!empty($this->route)){

            $route_transfer = \DB::table('route_transfer')
                ->where('route_id',$this->route->id)
                ->where('partner_id',$this->reservation->partner_id)
                ->where('transfer_id',$this->reservation->transfer_id)
                ->get()->first();


            if($route_transfer->opera_package_id){
                $this->packageID = $route_transfer->opera_package_id;
                $return = true;
            }
        }

        return $return;
    }

    /**
     * Function used to prepare the request needed to be sent towards Opera API Interface
     * @return void
     */
    private function buildRequestStruct(): bool{

        #Auth Credentials
        $this->request = $this->auth_credentials;
        #Resort - Point Destination Code
        $this->request['Resort'] = $this->resortPMSCode;
        #PMSReservationID - Reservation Number
        $this->request['PMSReservationID'] = $this->reservation_opera_id;
        #Transaction ID - Internal Booking ID
        $this->request['TransactionID'] = $this->reservation->id;

        #Build Packages
        $this->request['Packages'][] = $this->buildReservationPackage($this->reservation);

        #If there is a return transfer
        if((int)$this->reservation->round_trip_id > 0){

            $returnReservation = Reservation::findOrFail($this->reservation->round_trip_id);

            if($returnReservation){
                $this->request['Packages'][] = $this->buildReservationPackage($returnReservation);
            }
        }

        return true;
    }

    private function buildReservationPackage(\App\Models\Reservation $reservation) : array{

        return array(
            #1 if booking is active - 0 if cancelled
            'Quantity' => $reservation->status == Reservation::STATUS_CONFIRMED ? 1 : 0,
            'PackageID' => $this->packageID,
            'PricePerUnit' => $this->parsePackagePrice($reservation),
            'PackageType' => ValamarOperaApi::VARIABLE_PACKAGE_PRICE,
            'ExternalCartID' => $reservation->id,
            'ExternalCartItemID' => $reservation->id,
            'StartDate' => Carbon::parse($reservation->date_time)->toDateString(),
            'EndDate' => Carbon::parse($reservation->date_time)->toDateString(),
            'Comment' => $this->buildPackageComment($reservation),
        );
    }
    /**
     * Function used to create the package description / info visible to reception
     * @return string Returning the package description
     */
    private function buildPackageComment(\App\Models\Reservation $reservation) : string{

        $return = 'Transfer:';

        #Pickup Time
        $return .= 'Time: '.Carbon::parse($reservation->date_time)->toTimeString('minute');

        #Gather Pickup and Dropoff point Location info
        $pickup_location = Point::find($reservation->pickup_location);
        $dropoff_location = Point::find($reservation->dropoff_location);

        #Route
        $return .= ' From: '.$pickup_location->name.' To: '.$dropoff_location->name.' ';

        #Flight Number
        if($reservation->flight_number){
            $return .= 'Flight Number: '.$reservation->flight_number.' ';
        }


        #Comment
        if($this->reservation->remark){
            $return .= 'Remark: '.$reservation->remark.' ';
        }

        return trim($return);
    }

    /**
     * Get the package price from the Reservation List
     * @return string Price of the package
     */
    private function parsePackagePrice(\App\Models\Reservation $reservation) : String{

        $total = 0;

        #Get total of all items on the data list
        if(!empty($reservation->price_breakdown)){
            foreach($reservation->price_breakdown as $price_item){
                $total += $price_item['amount']['amount'];
            }
        }

        $money = new Money($total,new Currency('EUR'));

        return number_format($money->getAmount()/100, 2, '.', '');
    }

    /**
     * Function used to send request towards the API
     * @return void
     */
    private function sendOperaRequest() : bool{

        $this->setCallURL('PackagePosting');

        $this->validateResponse(
            Http::post($this->callURL,$this->request));

        return false;
    }

    /**
     * @param $method String - allowed methods PackagePosting
     * @return void
     */
    private function setCallURL($method) : void{
        $this->callURL = config('valamar.valamar_opera_api_url')."/".$method;
    }

    /**
    * @param \Illuminate\Http\Client\Response $response The Curl Response
    * @return ValamarOperaApi
    * @throws \Illuminate\Http\Client\RequestException Throw Exception with the appropriate error
    */
    private function validateResponse(\Illuminate\Http\Client\Response $response) : ValamarOperaApi
    {
       if(!$response->successful()){

            if($response->serverError()){
                $this->writeCommunicationLog(self::STATUS_ERROR);
                $response->throw($response->serverError());
            }else{
                $this->writeCommunicationLog(self::STATUS_ERROR);
                $response->throw($response->clientError());
            }

        }else{

            $this->responseBody = $response->json();


            if($this->responseBody['Status'] == 'ERR'){

                $this->writeCommunicationLog(self::STATUS_ERROR);
                $response->throw('An Error Has occured');
            }else{
                $this->writeCommunicationLog(self::STATUS_SUCCESS);
            }
        }

        return $this;
    }

    /**
     * @param array $error_list List of the errors returned by the API
     * @return string Formatted string consisting out of errors
     */
    private function buildErrorOutput(array $error_list) : string{

        $return = 'Error';

        if(!empty($error_list)){
            foreach($error_list as $err){
                $return .= $err."\n";
            }
        }

        return $return;
    }

    /**
     * @return void Write Opera Communication Log
     */
    private function writeCommunicationLog($status) : void{

        $log_message = '';
        #Update Opera Reservation Status
        switch ($status){
            case self::STATUS_SUCCESS:
                $this->reservation->opera_sync = 1;
                $log_message = 'Opera Sync Success';
                break;
            case self::STATUS_ERROR:

                $this->reservation->opera_sync = 0;

                if(empty($this->responseBody['Status'])){
                    if(!empty($this->errors)){
                        $log_message = $this->errors[0];
                    }
                }else{

                    if(!empty($this->responseBody['Packages'])){
                        foreach($this->responseBody['Packages'] as $package){
                            if(!empty($package['ErrorList'])){
                                $log_message = $package['ErrorList'][0];
                            }
                        }
                    }

                    if(!empty($this->responseBody['ErrorList'])){
                        $log_message = $this->responseBody['ErrorList'][0];
                    }
                }
                break;
        }

        $this->reservation->save();

        \DB::insert('insert into opera_sync_log (log_message,reservation_id, opera_request,opera_response,sync_status,updated_by,updated_at) values (?, ?, ?, ?, ?, ?, ?)',
            [
                $log_message,
                $this->reservation->id,
                json_encode($this->request),
                json_encode($this->responseBody),
                $status,
                auth()->user()->id,
                \Carbon\Carbon::now()->toDateTimeString()]
        );

    }

    /**
     * @param $reservation_id Reservation ID
     * @return void array Log Data
     */
    static function getSyncOperaLog($reservation_id){

        $log = \DB::table('opera_sync_log')
            ->where('reservation_id','=',$reservation_id)
            ->orderBy('id','desc')
            ->limit(1)
            ->get();

        if(!empty($log) && isset($log[0])){


            $log = $log[0];

            $log->opera_request = json_decode($log->opera_request,true);
            $log->opera_response = json_decode($log->opera_response,true);
        }else{
            $log = array(
                'id' => '0',
                'updated_at' => 'test',
                'log_message' => 'No Log',
                'opera_request' => 'No Request Body',
                'opera_response' => 'No Response Body',
            );
        }

        return json_decode(json_encode($log),true);
    }
}
