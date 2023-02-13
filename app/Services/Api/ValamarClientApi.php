<?php

namespace App\Services\Api;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ValamarClientApi{

    private string $request;
    private array $authHeaders;
    private string $callURL;
    private array $responseBody;
    private string $apiDateFormat = 'Y-m-d';
    private int $reservationSearchTimeout = 20;
    private array $propertiesList;
    private array $reservationsList;
    private string $bearer_token;

    private string $firstName;
    private string $lastName;
    private Carbon $checkIn;
    private Carbon $checkOut;
    private string $propertyPMSCode;
    private string $reservationCode;

    private array $reservationListFilters;
    private bool $reservationListFilterSet = false;

    public function __construct()
    {
        $this->setAuthHeaders();
    }

    /**
     * Fetch Current Properties list from Valamar
     * This will include the Property PMS code, Class and Property Name
     * @return array
     */
    public function getPropertiesList() : array
    {

        $this->getBearerToken();

        $this->authHeaders['Authorization'] = 'Bearer '.$this->bearer_token;

        $this->propertiesList = array();

        $this->setCallURL('properties');

        $this->validateResponse(
            Http::withHeaders($this->authHeaders)->get($this->callURL))
            ->validatePropertyList();

        return $this->propertiesList;
    }

    private function getBearerToken(){

        $this->setLoginCallURL('token');

        $bearerLogin = array(
            'clientId' => config('valamar.valamar_client_id'),
            'clientSecret' => config('valamar.valamar_client_secret')
        );


        $this->validateResponse(
            Http::withHeaders($this->authHeaders)->post($this->callURL,$bearerLogin))->validateBearerToken();
    }

    private function validateBearerToken() : void
    {
        if(!empty($this->responseBody)){
            if(!empty($this->responseBody['token'])){
                $this->bearer_token = $this->responseBody['token'];
            }
        }
    }

    /**
     * Opera Property Name, PMS code and PMS class are mandatory
     * In case any of these are missing, the data is non valid
     * This function removes all the entries from the response that are missing any of the parameters
     * @return void
     */
    private function validatePropertyList() : void
    {
        if(!empty($this->responseBody)){
            foreach($this->responseBody as $k => $prop){
                $entry_valid = true;

                if(empty($prop['propertyOperaCode'])){
                    $entry_valid = false;
                }elseif (empty($prop['name'])){
                    $entry_valid = false;
                }elseif(empty($prop['class'])){
                    $entry_valid = false;
                }

                if($entry_valid){
                  $this->propertiesList[] = $prop;
                }
            }
        }
    }
    /**
     * Get reservation details based on the passed parameters
     * In case Reservation Number is passed, the other request filters are going to be omitted
     * @return array
     */
    public function getReservationList() : array
    {
        $this->getBearerToken();

        $this->authHeaders['Authorization'] = 'Bearer '.$this->bearer_token;

        #Return Empty Result set in case no filters are set - optimize by avoiding the call
        $this->reservationsList = array();

        $this->configureReservationListFilters();

        if($this->isReservationListFilterDefined()) {

            $this->setCallURL('reservations');


            $this->validateResponse(
                Http::withHeaders($this->authHeaders)
                        ->timeout($this->reservationSearchTimeout)
                        ->get($this->callURL,$this->reservationListFilters))
            ->validateReservationList();
        }

        return $this->reservationsList;

    }

    /**
     * Function used to validate and format the reservation output
     * @return void
     */
    private function validateReservationList() : void
    {

        if(!empty($this->responseBody)){
            foreach($this->responseBody as $reservation){


                #Format CheckIn and Checkout
                $reservation['checkIn'] = substr(trim($reservation['checkIn']),0,10);
                $reservation['checkOut'] = substr(trim($reservation['checkOut']),0,10);
                #Generate Key for Phone Number
                if(empty($reservation['reservationHolderData']['mobile'])){
                    $reservation['reservationHolderData']['mobile'] = "";
                }

                #Set Associative Array - Reservation Code - key based
                if(!empty($reservation['reservationPhobsCode'])){
                    $this->reservationsList[$reservation['reservationPhobsCode']] = $reservation;
                }else{
                    if(!empty($reservation['OPERA']['RESV_NAME_ID'])){
                        $this->reservationsList[$reservation['OPERA']['RESV_NAME_ID']] = $reservation;
                    }
                }

            }
        }
    }

    /**
     * Function used to configure and adapt the logic of the reservation Search Filter
     * @return void
     */
    private function configureReservationListFilters() : void
    {
        $this->reservationListFilters = array();
        #Case Reservation Code is present - everything else is omitted
        if(!empty($this->getReservationCodeFilter())){
            $this->reservationListFilters['ReservationCode'] = $this->getReservationCodeFilter();
        }else{
            #CheckIn Filter
            if(!empty($this->getCheckInFilter())){
                $this->reservationListFilters['CheckIn'] = $this->getCheckInFilter();
            }

            #CheckOut Filter
            if(!empty($this->getCheckOutFilter())){
                $this->reservationListFilters['CheckOut'] = $this->getCheckOutFilter();
            }

            #First Name Filter
            if(!empty($this->getFirstNameFilter())){
                $this->reservationListFilters['FirstName'] = $this->getFirstNameFilter();
            }

            #Last Name Filter
            if(!empty($this->getLastNameFilter())){
                $this->reservationListFilters['LastName'] = $this->getLastNameFilter();
            }

            #PMS Property Code
            if(!empty($this->getPropertyPMSCodeFilter())){
                $this->reservationListFilters['PropertyPmsCode'] = $this->getPropertyPMSCodeFilter();
            }

        }

        #Filter Toggle
        if(!empty($this->reservationListFilters)){
            $this->reservationListFilterSet = true;
        }
    }

    /**
     * @return bool Returns true or false depending if filter is set or not
     */
    public function isReservationListFilterDefined() : bool
    {
        return $this->reservationListFilterSet;
    }
    /**
     * Setting authentication Headers for the Call
     * @return void
     */
    private function setAuthHeaders()
    {
        $this->authHeaders = array(
            'Cache-Control' => 'no-cache',
            'Ocp-Apim-Subscription-Key' => config('valamar.valamar_client_api_key')
        );
    }

    /**
     * @param $method String - allowed methods properties|reservations
     * @return void
     */
    private function setCallURL($method) : void
    {
       $this->callURL = config('valamar.valamar_client_api_url')."/".$method;
    }

    /**
     * @param $method String - allowed methods properties|reservations
     * @return void
     */
    private function setLoginCallURL($method) : void
    {
       $this->callURL = config('valamar.valamar_api_login_url')."/".$method;
    }

    /**
     * @param \Illuminate\Http\Client\Response $response The Curl Response
     * @return ValamarClientApi
     * @throws \Illuminate\Http\Client\RequestException Throw Exception with the appropriate error
     */
    private function validateResponse(\Illuminate\Http\Client\Response $response) : ValamarClientApi
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


    /**
     *  Function used to set the check in filter
     * @param Carbon $checkIn
     * @return $this
     */
    public function setCheckInFilter(Carbon $checkIn) : ValamarClientApi
    {
        $this->checkIn =  $checkIn;
        return $this;
    }

    /**
     *  Function used to set the check in filter
     * @param Carbon $checkOut
     * @return $this
     */
    public function setCheckOutFilter(Carbon $checkOut) : ValamarClientApi
    {
        $this->checkOut =  $checkOut;
        return $this;
    }

    /**
     * @return bool|string Returns the set checkin date or false if not set
     */
    private function getCheckInFilter(): bool|string
    {
        return !empty($this->checkIn) ? $this->checkIn->format($this->apiDateFormat) : false;
    }

    /**
     * @return bool|string Returns the set checkout date or false if not set
     */
    private function getCheckOutFilter(): bool|string
    {

        ##Fallback if only check-in is provided - checkout is passed as the same date
        if($this->getCheckInFilter() && empty($this->checkOut)){
            $this->checkOut = Carbon::createFromFormat($this->apiDateFormat,$this->getCheckInFilter());
        }

        return !empty($this->checkOut) ? $this->checkOut->format($this->apiDateFormat) : false;
    }

    /**
     * @param $reservationCode The reservation number to query by
     * If reservationCode parameter is set, other reservation filters are omited
     * Lowercased by Valamar documentation
     * @return void
     */
    public function setReservationCodeFilter($reservationCode) : ValamarClientApi
    {
        $this->reservationCode = trim(strtolower($reservationCode));
        return $this;
    }

    /**
     * @return bool|string Returning false if reservation number filter is not set, otherwise returning the reservation number
     */
    private function getReservationCodeFilter() : bool|string
    {
        return !empty($this->reservationCode) ?  strtolower($this->reservationCode) : false;
    }


    /**
     * @param $pmsCode Property Opera PMS Code
     * @return void
     */
    public function setPropertyPMSCodeFilter($pmsCode) : ValamarClientApi
    {
        $this->propertyPMSCode = trim($pmsCode);
        return $this;
    }

    /**
     * If propertyPMSCode is sent as a parameter, PMS Code is returned, false otherwise
     * @return bool|string
     */
    public function getPropertyPMSCodeFilter() : bool|string
    {
        return !empty($this->propertyPMSCode) ? $this->propertyPMSCode : false;
    }


    /**
     * Function used to set the first name filter
     * @param $firstName Guest first name to search the booking by
     * @return $this
     */
    public function setFirstNameFilter($firstName) : ValamarClientApi
    {
        $this->firstName = trim($firstName);
        return $this;
    }


    /**
     * @return bool|string First Name filter if set, false otherwise
     */
    private function getFirstNameFilter() : bool|string
    {
        return !empty($this->firstName) ?  $this->firstName : false;
    }
    /**
     * Function used to set the first name filter
     * @param $firstName Guest first name to search the booking by
     * @return $this
     */
    public function setLastNameFilter($lastName) : ValamarClientApi
    {
        $this->lastName = trim($lastName);
        return $this;
    }


    /**
     * @return bool|string Last Name filter if set, false otherwise
     */
    private function getLastNameFilter() : bool|string
    {
        return !empty($this->lastName) ?  $this->lastName : false;
    }
}
