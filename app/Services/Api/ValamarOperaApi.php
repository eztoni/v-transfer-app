<?php

namespace App\Services\Api;
use App\Models\Point;
use App\Models\Reservation;
use App\Models\Route;
use App\Models\Transfer;
use App\Models\Traveller;
use App\Models\Partner;
use Carbon\Carbon;
use Exchanger\Exception\Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\App;
use Cknow\Money\Money;
use Money\Currency;

class ValamarOperaApi{

    private array $auth_credentials = array();
    private Reservation $reservation;
    private Reservation $round_trip_reservation;
    private string $reservation_opera_id = '';
    private string $resortPMSCode = '';
    private array $errors;
    private Traveller $lead_traveller;
    private array $request = array();
    private array $responseBody = array();
    private string $packageID;
    private string $returnPackageID;
    private string $callURL;
    private Route $route;
    private Route $return_route;

    private $cf_only = false;
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

    public function syncReservationWithOperaFull($reservation_id,$cf_only = false){

        $this->cf_only = $cf_only;

        $this->reservation = Reservation::findOrFail($reservation_id);

        if($this->reservation->isVLevelReservation()){

            $valamarAlertAPI = new ValamarAlertApi();
            $valamarAlertAPI->setReservation($this->reservation);
            $valamarAlertAPI->sendAlert();
            return true;
        }

        $this->validateReservationMapping();

        if(empty($this->errors)){

            $this->buildCoreRequestStruct();

            $this->request['Packages'] = $this->buildReservationPackages($this->reservation);

            if(empty($this->errors)){
                $this->sendOperaRequest();
            }else{
                $this->request = $this->errors;
                $this->writeCommunicationLog(self::STATUS_ERROR);
            }
        }else{

            $this->request = $this->errors;
            $this->writeCommunicationLog(self::STATUS_ERROR);
        }
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

    public function syncReservationCFWithOpera($reservation_id,$cancellation_fee,$no_show = false){

        $this->reservation = Reservation::findOrFail($reservation_id);

        if($this->validateCFReservationMapping($no_show)){
            if($this->buildCFRequestStruct($cancellation_fee,$no_show)){
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
     * Function used to validate whether all the parameters have been set
     * @return bool Returns true if all the properties for the reqest have the valid mapping structure
     */
    private function validateCFReservationMapping($no_show = false) : bool{

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

        if(!$no_show){
            #Validate Cancellation PackageID - Route PMS Code
            if(!$this->validateCancellationPackageID()){
                $return = false;
                $this->errors[] = 'Missing Cancellation Opera Package ID';
            }
        }else{
            #Validate Cancellation PackageID - Route PMS Code
            if(!$this->validateNoShowPackageID()){
                $return = false;
                $this->errors[] = 'Missing NoShow Opera Package ID';
            }
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

            $lowercase_keys = array_change_key_case($res_result, CASE_LOWER);
            $reservation_number = strtolower($this->reservation->getLeadTravellerAttribute()->reservation_number);

            if (!empty($lowercase_keys[$reservation_number])) {

                $this->resortPMSCode = $lowercase_keys[$reservation_number]['propertyOperaCode'];

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

        #Check if there is a RoundTripID
        if($this->reservation->round_trip_id && $return === true){

            #Reset to false as both need to be present in order to send it to Opera
            $return = false;

            $this->round_trip_reservation = Reservation::findOrFail($this->reservation->round_trip_id);

            $this->return_route = Route::query()
                ->where('destination_id', $this->round_trip_reservation->destination_id)
                ->where('starting_point_id', $this->round_trip_reservation->pickup_location)
                ->where('ending_point_id', $this->round_trip_reservation->dropoff_location)
                ->get()->first();



            if($this->return_route){

                $return_route_transfer = \DB::table('route_transfer')
                    ->where('route_id',$this->return_route->id)
                    ->where('partner_id',$this->round_trip_reservation->partner_id)
                    ->where('transfer_id',$this->round_trip_reservation->transfer_id)
                    ->get()->first();

                $this->returnPackageID = $return_route_transfer->opera_package_id;
                $return = true;
            }
        }

        return $return;
    }
    /**
     * Function used to validate that the reservation number has been set for this reservation
     * @return bool True if the reservation number has been set for this reservation, false otherwise
     */
    private function validateCancellationPackageID() : bool{

        $return = false;

        $partner = Partner::findOrFail($this->reservation->partner_id);

        if(!empty($partner)){
            if($partner->cancellation_package_id){
                $this->packageID = $partner->cancellation_package_id;
                return true;
            }
        }

        return $return;
    }
    /**
     * Function used to validate that the reservation number has been set for this reservation
     * @return bool True if the reservation number has been set for this reservation, false otherwise
     */
    private function validateNoShowPackageID() : bool{

        $return = false;

        $partner = Partner::findOrFail($this->reservation->partner_id);

        if(!empty($partner)){
            if($partner->no_show_package_id){
                $this->packageID = $partner->no_show_package_id;
                return true;
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
        $this->request['Packages'] = $this->buildReservationPackage($this->reservation);

        return true;
    }

    /**
     * Function used to prepare the request needed to be sent towards Opera API Interface
     * @return void
     */
    private function buildCoreRequestStruct(): bool{

        #Auth Credentials
        $this->request = $this->auth_credentials;
        #Resort - Point Destination Code
        $this->request['Resort'] = $this->resortPMSCode;
        #PMSReservationID - Reservation Number
        $this->request['PMSReservationID'] = $this->reservation_opera_id;
        #Transaction ID - Internal Booking ID
        $this->request['TransactionID'] = $this->reservation->id;

        return true;
    }
    /**
     * Function used to prepare the request needed to be sent towards Opera API Interface
     * @return void
     */
    private function buildCFRequestStruct($cancellation_fee,$no_show = false): bool{

        #Auth Credentials
        $this->request = $this->auth_credentials;
        #Resort - Point Destination Code
        $this->request['Resort'] = $this->resortPMSCode;
        #PMSReservationID - Reservation Number
        $this->request['PMSReservationID'] = $this->reservation_opera_id;
        #Transaction ID - Internal Booking ID
        $this->request['TransactionID'] = $this->reservation->id;

        #Build Packages
        $this->request['Packages'][] = $this->buildCFPackage($this->reservation,$cancellation_fee,$no_show);

        return true;
    }

    private function buildReservationPackage(\App\Models\Reservation $reservation) : array{

        $accommodation_res_checkout = $reservation->getLeadTravellerAttribute()?->reservation_check_out;

        if($reservation->round_trip_id){

            $return = array();

            $package_date = Carbon::parse($reservation->date_time)->toDateString();

            $package_start = $package_date;
            $package_end = $package_date;


            if($package_date == $accommodation_res_checkout){
                $package_start = $reservation->date_time->subDays(1)->toDateString();
            }

            $return[] = array(
                #1 if booking is active - 0 if cancelled
                'Quantity' => $reservation->status == Reservation::STATUS_CONFIRMED ? 1 : 0,
                'PackageID' => $this->packageID,
                'PricePerUnit' => $this->parsePackagePrice($reservation,1),
                'PackageType' => ValamarOperaApi::VARIABLE_PACKAGE_PRICE,
                'ExternalCartID' => $reservation->id,
                'ExternalCartItemID' => $reservation->id,
                'StartDate' => $package_start,
                'EndDate' => $package_end,
                'Comment' => $this->buildPackageComment($reservation),
            );


            $package_date = Carbon::parse($this->round_trip_reservation->date_time)->toDateString();
            $package_start = $package_date;
            $package_end = $package_date;

            if($package_date == $accommodation_res_checkout){
                $package_start = $this->round_trip_reservation->date_time->subDays(1)->toDateString();
            }



            #Round Trip Booking
            $return[] = array(
                #1 if booking is active - 0 if cancelled
                'Quantity' => $this->round_trip_reservation->status == Reservation::STATUS_CONFIRMED ? 1 : 0,
                'PackageID' => $this->returnPackageID,
                'PricePerUnit' => $this->parsePackagePrice($reservation,2),
                'PackageType' => ValamarOperaApi::VARIABLE_PACKAGE_PRICE,
                'ExternalCartID' => $this->round_trip_reservation->id,
                'ExternalCartItemID' => $this->round_trip_reservation->id,
                'StartDate' => $package_start,
                'EndDate' => $package_end,
                'Comment' => $this->buildPackageComment($this->round_trip_reservation),
            );

            return $return;

        }else{

            $return = array();

            $package_date = Carbon::parse($reservation->date_time)->toDateString();

            $package_start = $package_date;
            $package_end = $package_date;


            if($package_date == $accommodation_res_checkout){
                $package_start = $reservation->date_time->subDays(1)->toDateString();
            }

            $return[] = array(
                #1 if booking is active - 0 if cancelled
                'Quantity' => $reservation->status == Reservation::STATUS_CONFIRMED ? 1 : 0,
                'PackageID' => $this->packageID,
                'PricePerUnit' => $this->parsePackagePrice($reservation),
                'PackageType' => ValamarOperaApi::VARIABLE_PACKAGE_PRICE,
                'ExternalCartID' => $reservation->id,
                'ExternalCartItemID' => $reservation->id,
                'StartDate' => $package_start,
                'EndDate' => $package_end,
                'Comment' => $this->buildPackageComment($reservation),
            );



            return $return;
        }

    }

    private function buildReservationPackages(\App\Models\Reservation $reservation) : array{

        $accommodation_reservation_checkout = $reservation->getLeadTravellerAttribute()?->reservation_check_out;

        #Case 1: Single Route Confirmed Booking
        if($reservation->status == Reservation::STATUS_CONFIRMED && !$reservation->isRoundTrip()){

            $this->validateReservationMapping();

            if(empty($this->errors)){

                $return = array();

                $package_date = Carbon::parse($reservation->date_time)->toDateString();

                $package_start = $package_date;
                $package_end = $package_date;


                if($accommodation_reservation_checkout){
                    if($package_date == $accommodation_reservation_checkout){
                        $package_start = $reservation->date_time->subDays(1)->toDateString();
                    }
                }

                $return[] = array(
                    #1 if booking is active
                    'Quantity' => 1,
                    'PackageID' => $this->packageID,
                    'PricePerUnit' => $this->parsePackagePrice($reservation),
                    'PackageType' => ValamarOperaApi::VARIABLE_PACKAGE_PRICE,
                    'ExternalCartID' => $reservation->id,
                    'ExternalCartItemID' => $reservation->id,
                    'StartDate' => $package_start,
                    'EndDate' => $package_end,
                    'Comment' => $this->buildPackageComment($reservation),
                );

                return $return;

            }else{
                dd($this->errors);
            }
        #Case 2 - One Way Reservation - Cancelled
        }elseif(!$reservation->isRoundTrip() && $reservation->isCancelled()){
            #First Cancel The Old Package
            $this->validateReservationMapping();

            if(empty($this->errors)){

                $return = array();

                $package_date = Carbon::parse($reservation->date_time)->toDateString();

                $package_start = $package_date;
                $package_end = $package_date;


                if($accommodation_reservation_checkout){
                    if($package_date == $accommodation_reservation_checkout){
                        $package_start = $reservation->date_time->subDays(1)->toDateString();
                    }
                }

                if(!$this->cf_only){
                    $return[] = array(
                        #Quantity 0 - Cancelling the old package
                        'Quantity' => 0,
                        'PackageID' => $this->packageID,
                        'PricePerUnit' => $this->parsePackagePrice($reservation),
                        'PackageType' => ValamarOperaApi::VARIABLE_PACKAGE_PRICE,
                        'ExternalCartID' => $reservation->id,
                        'ExternalCartItemID' => $reservation->id,
                        'StartDate' => $package_start,
                        'EndDate' => $package_end,
                        'Comment' => $this->buildPackageComment($reservation),
                    );
                }

                ##See if there is a cancellation Fee
                if($reservation->hasCancellationFee()){

                    $this->validateCFReservationMapping($reservation->cancellation_type == 'no_show' ? true : false);

                    if(empty($this->errors)){

                        $cf = $reservation->cancellation_fee;

                        if($this->reservation->cf_null == 1){
                            $cf = 0;
                        }

                        $return[] = $this->buildCFPackage($reservation,$cf,$reservation->cancellation_type == 'no_show' ? true : false);

                        dd($return);
                    }
                }


                return $return;

            }else{
                dd($this->errors);
            }
        }elseif($reservation->isRoundTrip()){

            $return = array();

            #If main reservation is triggered
            if($reservation->is_main){

                $package_date = Carbon::parse($reservation->date_time)->toDateString();

                $package_start = $package_date;
                $package_end = $package_date;

                if($package_date == $accommodation_reservation_checkout){
                    $package_start = $reservation->date_time->subDays(1)->toDateString();
                }

                if(!$this->cf_only){
                    $return[] = array(
                        #1 if booking is active - 0 if cancelled
                        'Quantity' => $reservation->status == Reservation::STATUS_CONFIRMED ? 1 : 0,
                        'PackageID' => $this->packageID,
                        'PricePerUnit' => $this->parsePackagePrice($reservation,1),
                        'PackageType' => ValamarOperaApi::VARIABLE_PACKAGE_PRICE,
                        'ExternalCartID' => $reservation->id,
                        'ExternalCartItemID' => $reservation->id,
                        'StartDate' => $package_start,
                        'EndDate' => $package_end,
                        'Comment' => $this->buildPackageComment($reservation),
                    );
                }


                #Check Cancellation Fee For The Booking
                if($reservation->isCancelled() && $reservation->hasCancellationFee()){
                    $this->validateCFReservationMapping($reservation->cancellation_type == 'no_show' ? true : false);


                    if(empty($this->errors)){

                        $cf = $reservation->cancellation_fee;

                        if($reservation->cf_null == 1){
                            $cf = 0;
                        }

                        $return[] = $this->buildCFPackage($reservation,$cf,$reservation->cancellation_type == 'no_show' ? true : false);

                    }
                }



                $this->round_trip_reservation = Reservation::findOrFail($reservation->round_trip_id);

                $package_date = Carbon::parse($this->round_trip_reservation->date_time)->toDateString();
                $package_start = $package_date;
                $package_end = $package_date;

                if($package_date == $accommodation_reservation_checkout){
                    $package_start = $this->round_trip_reservation->date_time->subDays(1)->toDateString();
                }

                if(!$this->cf_only){

                    #Round Trip Booking
                    $return[] = array(
                        #1 if booking is active - 0 if cancelled
                        'Quantity' => $this->round_trip_reservation->status == Reservation::STATUS_CONFIRMED ? 1 : 0,
                        'PackageID' => $this->returnPackageID,
                        'PricePerUnit' => $this->parsePackagePrice($this->round_trip_reservation,2),
                        'PackageType' => ValamarOperaApi::VARIABLE_PACKAGE_PRICE,
                        'ExternalCartID' => $this->round_trip_reservation->id,
                        'ExternalCartItemID' => $this->round_trip_reservation->id,
                        'StartDate' => $package_start,
                        'EndDate' => $package_end,
                        'Comment' => $this->buildPackageComment($this->round_trip_reservation),
                    );
                }

                if($this->round_trip_reservation->isCancelled() && $this->round_trip_reservation->hasCancellationFee()){

                    $this->validateCFReservationMapping($this->round_trip_reservation->cancellation_type == 'no_show' ? true : false);

                    if(empty($this->errors)){

                        $cf = $this->round_trip_reservation->cancellation_fee;

                        if($this->round_trip_reservation->cf_null == 1){
                            $cf = 0;
                        }

                        $return[] = $this->buildCFPackage($this->round_trip_reservation,$cf,$this->round_trip_reservation->cancellation_type == 'no_show' ? true : false);
                    }
                }

                return $return;
            }
        }

    }

    private function buildCFPackage(\App\Models\Reservation $reservation,$cancellation_fee,$no_show = false) : array{

        $accommodation_res_checkout = $reservation->getLeadTravellerAttribute()?->reservation_check_out;

        $comment = 'Cancellation Fee';

        if($no_show){
            $comment = 'NoShow fee';
        }

        $package_date = Carbon::parse($reservation->date_time)->toDateString();

        $package_start = $package_date;
        $package_end = $package_date;


        if($package_date == $accommodation_res_checkout){
            $package_start = $reservation->date_time->subDays(1)->toDateString();
        }

        return array(
            #1 if booking is active - 0 if cancelled
            'Quantity' => 1,
            'PackageID' => $this->packageID,
            'PricePerUnit' => $cancellation_fee,
            'PackageType' => ValamarOperaApi::VARIABLE_PACKAGE_PRICE,
            'ExternalCartID' => $reservation->id,
            'ExternalCartItemID' => $reservation->id,
            'StartDate' => $package_start,
            'EndDate' => $package_end,
            'Comment' => $comment,
        );
    }
    /**
     * Function used to create the package description / info visible to reception
     * @return string Returning the package description
     */
    private function buildPackageComment(\App\Models\Reservation $reservation) : string{



        $pickup_address = Point::find($reservation->pickup_address_id);
        $dropoff_address = Point::find($reservation->dropoff_address_id);

        if(!$reservation->is_round_trip){
            ##Address bug
            $fa = $pickup_address;
            $pickup_address = $dropoff_address;
            $dropoff_address = $fa;
        }

        $prefix = 'IN# ';

        if($pickup_address->type != 'airport'){
            $prefix = 'OUT# ';
        }

        $return = $prefix.'Transfer:';

        #Pickup Time
        $return .= 'Time: '.Carbon::parse($reservation->date_time)->toTimeString('minute');

        #Gather Pickup and Dropoff point Location info
        $pickup_location = Point::find($reservation->pickup_location);
        $dropoff_location = Point::find($reservation->dropoff_location);

        #Route
        $return .= ' From: '.$pickup_address->name.' To: '.$dropoff_address->name.' ';

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
    private function parsePackagePrice(\App\Models\Reservation $reservation,$direction = 0) : String{

        $total = 0;


        if($direction == 0){
            #Get total of all items on the data list
            if(!empty($reservation->price_breakdown)){
                foreach($reservation->price_breakdown as $price_item){
                    $total += $price_item['amount']['amount'];
                }
            }
        }



        if($direction == 1 || $direction == 2){
            if(!empty($reservation->price_breakdown)){

                foreach($reservation->price_breakdown as $price_item){
                  if($price_item['item'] == 'transfer_price'){

                      if($direction == 1){
                          $total += $price_item['price_data']['price'];
                      }

                      if($direction == 2){
                          $total += $price_item['price_data']['price'];
                      }
                  }
                }
            }

        }

        $money = new Money($reservation->price,new Currency('EUR'));

        return number_format($money->getAmount()/100, 2, '.', '');
    }

    /**
     * Function used to send request towards the API
     * @return void
     */
    private function sendOperaRequest() : bool{

        $this->setCallURL('PackagePosting');

        if(App::environment('local')){
            $this->write_opera_log();
        }else{
            $this->validateResponse(
                Http::post($this->callURL,$this->request));
        }

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


            if($this->responseBody['Status'] != 'OK'){

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

        $user_id = 0;

        if(auth()->user()){
            $user_id = auth()->user()->id;
        }

        \DB::insert('insert into opera_sync_log (log_message,reservation_id, opera_request,opera_response,sync_status,updated_by,updated_at) values (?, ?, ?, ?, ?, ?, ?)',
            [
                $log_message,
                $this->reservation->id,
                json_encode($this->request),
                json_encode($this->responseBody),
                $status,
                $user_id,
                \Carbon\Carbon::now()->toDateTimeString()]
        );

    }

    /**
     * @param $reservation_id Reservation ID
     * @return void array Log Data
     */
    static function getSyncOperaLog($reservation_id,$log_id = false){

        $return = array();

        $log_list = \DB::table('opera_sync_log')
                ->where('reservation_id','=',$reservation_id)
                ->orderBy('id','desc')
                ->get();

            if(!empty($log_list)){
               foreach($log_list as $log){
                    $return[$log->id] = $log;
               }
            }


            if($log_id){
                if(!empty($return[$log_id])){
                    $log = array();
                    $log[$log_id] = $return[$log_id];

                    $return = $log;

                }
            }

            return $return;
    }

    private function write_opera_log(){

        $log = '['.Carbon::now()->toDateTimeString().']'."\n";

        $log .= print_r($this->request,true)."\n\n";

        file_put_contents('opera_local_log.txt',$log,FILE_APPEND);
    }
}
