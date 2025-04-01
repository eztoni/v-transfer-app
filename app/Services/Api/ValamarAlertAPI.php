<?php

namespace App\Services\Api;
use App\BusinessModels\Reservation\Reservation;
use App\Models\Destination;
use App\Models\Owner;
use App\Scopes\DestinationScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ValamarAlertApi{

    const CALL_METHOD_ALERT_POSTING = 'AlertPosting';
    const ALERT_ACTION_ADD = 'ADD';
    const ALERT_ACTION_DELETE = 'DEL';
    const FIELD_SYS_USER = 'SysUser';
    const FIELD_SYS_PASS = 'SysPass';
    const FIELD_RESORT = 'Resort';
    const FIELD_PMS_RESERVATION_ID  = 'PMSReservationID';
    const FIELD_ALERT_TEXT = 'AlertText';
    const FIELD_ACTION = 'Action';

    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';

    private $errors = array();
    private $request = array();

    private $response = array();

    private $callURL = '';

    private $reservation;

    private $resortPMSCode;

    private $reservationOperaID;

    private $mandatoryFields = array(
            self::FIELD_SYS_USER,
            self::FIELD_SYS_PASS,
            self::FIELD_RESORT,
            self::FIELD_PMS_RESERVATION_ID,
            self::FIELD_ALERT_TEXT,
            self::FIELD_ACTION
    );

    function __construct(){
        $this->setAuthenticationCredentials();
    }

    private function setAuthenticationCredentials(){

        $this->request[self::FIELD_SYS_USER] =  config('valamar.valamar_opera_api_user');
        $this->request[self::FIELD_SYS_PASS] =  config('valamar.valamar_opera_api_pass');

        $this->setCallURL();
    }

    private function setCallURL() : void{
        $this->callURL = config('valamar.valamar_opera_api_url')."/".self::CALL_METHOD_ALERT_POSTING;
    }

    public function setReservation(\App\Models\Reservation $reservation){
        $this->reservation = $reservation;
    }

    public function sendAlert(){

        if($this->validateReservationMapping()){

            #Delete All Previous Alerts on the Reservation
            $this->callService($this->buildDeleteRequestStruct());

            #Add New Request Struct
            $this->callService($this->buildRequestStruct());
        }


    }

    private function buildDeleteRequestStruct(){

        #Resort - Point Destination Code
        $this->request[self::FIELD_RESORT] = $this->resortPMSCode;
        #PMSReservationID - Reservation Number
        $this->request[self::FIELD_PMS_RESERVATION_ID] = $this->reservationOperaID;
        #Action
        $this->request[self::FIELD_ACTION] = self::ALERT_ACTION_DELETE;

        return true;
    }

    private function buildRequestStruct(){

        #Resort - Point Destination Code
        $this->request[self::FIELD_RESORT] = $this->resortPMSCode;
        #PMSReservationID - Reservation Number
        $this->request[self::FIELD_PMS_RESERVATION_ID] = $this->reservationOperaID;
        #Alert Text
        $this->request[self::FIELD_ALERT_TEXT] = $this->buildAlertText();
        #Action
        $this->request[self::FIELD_ACTION] = self::ALERT_ACTION_ADD;

        if($this->request[self::FIELD_ALERT_TEXT] === false){
            return false;
        }

        return true;

    }

    private function buildAlertText(){

        $message = false;

        #If triggered by main booking
        if($this->reservation->is_main == 0){
            $this->reservation = \App\Models\Reservation::where('round_trip_id',$this->reservation->id)->get()->first();
        }

        $destination = Destination::findOrFail($this->reservation->destination_id);

        $owner = Owner::findOrFail($destination->owner_id);

        $cancellation_message = 'Gost je kasno otkazao uključeni transfer - '.$owner->name.', '.$destination->name.' #ID: '.$this->reservation->id.'.  Rezervacija je otkazana u Operi,te je primjenjen cancellation fee od 100% iznosa rezervacije transfera.';

        $one_way_reservation_message = 'Gost ima uključeni transfer, ubaciti paket u rezervaciju - '.$owner->name.', '.$destination->name.' #ID: '.$this->reservation->id;

        #Add Vehicle Type
        $one_way_reservation_message .= ' - Vrsta Vozila: '.$this->reservation->transfer->vehicle->type;




        #One way booking
        if(!$this->reservation->isRoundTrip()){
            if(!$this->reservation->isCancelled()){
                $message = $one_way_reservation_message;
            }elseif($this->reservation->isLateCancellation()){
                $message = $cancellation_message;
            }else{
                return false;
            }
        }else{

            #If both directions are confirmed
            if(!$this->reservation->isCancelled() && !$this->reservation->returnReservation->isCancelled()){
                $message = $one_way_reservation_message.' (round trip)';
            }elseif($this->reservation->isCancelled() && $this->reservation->returnReservation->isCancelled()){

                #If both directions are cancelled
                if($this->reservation->isLateCancellation()){
                    $message = $cancellation_message;
                }else{
                    return false;
                }
            }elseif($this->reservation->returnReservation->isCancelled()){
                #Only the first direction is active
                $message = $one_way_reservation_message;
            }else{
                #Only the second direction is active
                $message = 'Gost ima uključeni transfer, ubaciti paket u rezervaciju - '.$owner->name.', '.$destination->name.' #ID: '.$this->reservation->returnReservation->id;
                $message .= ' - Vrsta Vozila: '.$this->reservation->transfer->vehicle->type;
            }
        }

        $reservationIdToExclude = $this->reservation->id; // <-- this should be your passed parameter

        $existingReservation = DB::table('reservations')
            ->join('reservation_traveller', 'reservations.id', '=', 'reservation_traveller.reservation_id')
            ->join('travellers', 'reservation_traveller.traveller_id', '=', 'travellers.id')
            ->where('reservations.status', 'confirmed')
            ->where('reservations.is_main', 1)
            ->where('reservations.id', '!=', $reservationIdToExclude)
            ->where('reservation_number',$this->reservation->getAccommodationReservationCode())
            ->select('reservations.id', 'travellers.full_name')
            ->first();

        if($existingReservation){

            $add_msg = $this->buildAdditionalText($existingReservation);

            if($add_msg){
                $message .= "\n[DODATNO]: ".$add_msg;
            }

        }
        return $message;
    }

    public function buildAdditionalText($booking)
    {
        $message = false;

        $reservation = \App\Models\Reservation::findOrFail($booking->id);

        // If triggered by return leg
        if ($reservation->is_main == 0) {
            $reservation = \App\Models\Reservation::where('round_trip_id', $reservation->id)->first();
        }

        $destination = Destination::findOrFail($reservation->destination_id);
        $owner = Owner::findOrFail($destination->owner_id);

        $cancellation_message = 'Gost je kasno otkazao uključeni transfer - ' . $owner->name . ', ' . $destination->name . ' #ID: ' . $reservation->id . '. Rezervacija je otkazana u Operi,te je primjenjen cancellation fee od 100% iznosa rezervacije transfera.';
        $one_way_reservation_message = 'Gost ima uključeni transfer, ubaciti paket u rezervaciju - ' . $owner->name . ', ' . $destination->name . ' #ID: ' . $reservation->id;

        // Add Vehicle Type if available
        if (!empty($reservation->transfer) && !empty($reservation->transfer->vehicle)) {
            $one_way_reservation_message .= ' - Vrsta Vozila: ' . $reservation->transfer->vehicle->type;
        }

        if (!$reservation->isRoundTrip()) {
            if (!$reservation->isCancelled()) {
                $message = $one_way_reservation_message;
            } elseif ($reservation->isLateCancellation()) {
                $message = $cancellation_message;
            } else {
                return false;
            }
        } else {
            $returnReservation = $reservation->returnReservation ?? null;

            if (!$reservation->isCancelled() && $returnReservation && !$returnReservation->isCancelled()) {
                $message = $one_way_reservation_message . ' (round trip)';
            } elseif (
                $reservation->isCancelled() &&
                $returnReservation &&
                $returnReservation->isCancelled()
            ) {
                if ($reservation->isLateCancellation()) {
                    $message = $cancellation_message;
                } else {
                    return false;
                }
            } elseif ($returnReservation && $returnReservation->isCancelled()) {
                $message = $one_way_reservation_message;
            } elseif ($returnReservation) {
                $message = 'Gost ima uključeni transfer, ubaciti paket u rezervaciju - ' . $owner->name . ', ' . $destination->name . ' #ID: ' . $returnReservation->id;

                if (!empty($reservation->transfer) && !empty($reservation->transfer->vehicle)) {
                    $message .= ' - Vrsta Vozila: ' . $reservation->transfer->vehicle->type;
                }
            }
        }

        return $message;
    }

    private function callService($request,$write_log = true){

        #if no request was provided
        if(empty($request)){
            return false;
        }

        $status = self::STATUS_SUCCESS;

        if($this->isLocalEnvironment()){
            file_put_contents('vlevel_alert_log.txt',$this->buildLogText(),FILE_APPEND);
            if($write_log){
                $this->writeCommunicationLog(self::STATUS_SUCCESS);
            }
        }else{
            $this->validateResponse(
                Http::post($this->callURL,$this->request));
        }
    }

    private function buildLogText(){

        $message = 'Unknown Request';

        switch ($this->request[self::FIELD_ACTION]){
            case self::ALERT_ACTION_DELETE:
                $message = 'Previous alerts successfully deleted.';
                break;
            case self::ALERT_ACTION_ADD:
                $message = 'Alert successfully added - '.$this->request[self::FIELD_ALERT_TEXT];
                break;
        }

        $log  = '[MESSAGE] - '.$message.' - '.Carbon::now()->format('d.m.Y h:i:s')."\n";
        $log .= '[REQUEST]'."\n";
        $log .= print_r($this->request,true)."\n";

        return $log;
    }

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

    private function validateReservationMapping() : bool{

        $return = true;

        #Validate that the Reservation is VLevel Reservation
        if(!$this->reservation->isVLevelReservation()){
            $return = false;
            $this->errors[] = 'Reservation #'.$this->reservation->id.' is not VLevel reservation';
        }

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
     * Function used to validate that the reservation number has been set for this reservation
     * @return bool True if the reservation number has been set for this reservation, false otherwise
     */
    private function validateReservationNumber() : bool{

        $return = false;

        $this->reservation->lead_traveller?->reservation_opera_id;

        if($this->reservation->lead_traveller?->reservation_opera_id != null){

            $this->reservationOperaID = $this->reservation->lead_traveller?->reservation_opera_id;
            $return = true;
        }

        return $return;
    }

    private function isLocalEnvironment(){

        $return = false;

        if(App::environment() == 'local'){
            $return = true;
        }

        return $return;
    }

    private function writeCommunicationLog($status) : void
    {

        $log_message = '';

        switch ($this->request[self::FIELD_ACTION]) {
            case self::ALERT_ACTION_DELETE:
                $log_message = 'Previous Alerts successfully deleted.';
                break;
            case self::ALERT_ACTION_ADD:
                $log_message = 'Alert successfully added - '.$this->request[self::FIELD_ALERT_TEXT];
                break;
        }

        #Update Opera Reservation Status
        switch ($status) {
            case self::STATUS_SUCCESS:
                $this->reservation->opera_sync = 1;
                break;
            case self::STATUS_ERROR:

                $this->reservation->opera_sync = 0;

                if (empty($this->response['Status'])) {
                    if (!empty($this->errors)) {
                        $log_message = $this->errors[0];
                    }
                } else {

                    if (!empty($this->responseBody['ErrorList'])) {
                        $log_message = $this->responseBody['ErrorList'][0];
                    }
                }
                break;
        }

        $this->reservation->save();

        $user_id = 0;

        if (auth()->user()) {
            $user_id = auth()->user()->id;
        }

        \DB::insert('insert into opera_sync_log (log_message,reservation_id, opera_request,opera_response,sync_status,updated_by,updated_at) values (?, ?, ?, ?, ?, ?, ?)',
            [
                $log_message,
                $this->reservation->id,
                json_encode($this->request),
                json_encode($this->response),
                $status,
                $user_id,
                \Carbon\Carbon::now()->toDateTimeString()]
        );
    }

    private function validateResponse(\Illuminate\Http\Client\Response $response) : ValamarAlertApi
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

            $this->response = $response->json();

            if($this->response['Status'] != 'OK'){

                $this->writeCommunicationLog(self::STATUS_ERROR);
                if($this->request[self::FIELD_ACTION] != 'DEL'){
                    $response->throw('An Error Has occurred');
                }
            }else{
                $this->writeCommunicationLog(self::STATUS_SUCCESS);
            }
        }

        return $this;
    }

}
