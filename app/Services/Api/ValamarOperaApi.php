<?php

namespace App\Services\Api;
use App\Models\Point;
use App\Models\Reservation;
use App\Models\Traveller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Cknow\Money\Money;
use Money\Currency;

class ValamarOperaApi{

    private array $auth_credentials = array();
    private Reservation $reservation;
    private string $reservation_number = '';
    private string $dropoffLocationPMSCode = '';
    private array $errors;
    private Traveller $lead_traveller;
    private array $request = array();
    private array $responseBody;
    private string $callURL;

    function __construct(){
        $this->setAuthenticationHeaders();
    }

    public function sendReservationToOpera($reservation_id){

        $this->reservation = Reservation::findOrFail($reservation_id);

        if($this->validateReservationMapping()){

            if($this->buildRequestStruct()){
                $this->sendOperaRequest();
            }
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
     * Function used to validate that the reservation number has been set for this reservation
     * @return bool True if the reservation number has been set for this reservation, false otherwise
     */
    private function validateReservationNumber() : bool{

        $return = false;

        $this->reservation->lead_traveller?->reservation_number;

        if($this->reservation->lead_traveller?->reservation_number != null){

            $this->reservation_number = $this->reservation->lead_traveller?->reservation_number;
            $return = true;
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
        $this->request['Resort'] = $this->dropoffLocationPMSCode;
        #PMSReservationID - Reservation Number
        $this->request['PMSReservationID'] = $this->reservation_number;
        #Transaction ID - Internal Booking ID
        $this->request['TransactionID'] = $this->reservation->id;


        #TODO - Remove
        $packageID = "PackageID";

        #Build Packages
        $this->request['Packages'][] = array(
            #Always Constant - 1
            'Quantity' => 1,
            'PackageID' => $packageID,
            'PricePerUnit' => $this->parsePackagePrice(),
            'PackageType' => 'FIX',
            'ExternalCardID' => $this->reservation->id,
            'ExternalCartItemID' => $this->reservation->id,
            'StartDate' => Carbon::parse($this->reservation->date_time)->toDateString(),
            'EndDate' => Carbon::parse($this->reservation->date_time)->toDateString(),
            'Comment' => $this->buildPackageComment(),
        );

        return true;
    }

    /**
     * Function used to create the package description / info visible to reception
     * @return string Returning the package description
     */
    private function buildPackageComment() : string{

        $return = 'Transfer: ';

        $pickup = Point::find($this->reservation->pickup_location);

        #Pickup Address
        $return .= "\nFrom: ".$pickup->name.', '.$this->reservation->pickup_address;

        #Pickup Time
        $return .= 'Time: '.Carbon::parse($this->reservation->date_time)->toTimeString();

        #Flight Number
        $return .= 'Flight Number: '.$this->reservation->flight_number;

        #Comment
        if($this->reservation->remark){
            $return .= 'Remark: '.$this->reservation->remark;
        }

        return $return;
    }

    /**
     * Get the package price from the Reservation List
     * @return string Price of the package
     */
    private function parsePackagePrice() : String{

        $total = 0;

        #Get total of all items on the data list
        if(!empty($this->reservation->price_breakdown)){
            foreach($this->reservation->price_breakdown as $price_item){
                $total += $price_item['amount']['amount'];
            }
        }

        $money = new Money($total,new Currency('EUR'));
        //TODO - check if Money has format output
        return number_format($money->getAmount(), 2);
    }

    /**
     * Function used to send request towards the API
     * @return void
     */
    private function sendOperaRequest() : bool{
        $this->setCallURL('PackagePosting');

        $this->validateResponse(
            Http::post($this->callURL,json_encode($this->request)));

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
                $response->throw($response->serverError());
            }else{
                $response->throw($response->clientError());
            }

        }else{
            $this->responseBody = $response->json();
        }

        return $this;
    }
}
