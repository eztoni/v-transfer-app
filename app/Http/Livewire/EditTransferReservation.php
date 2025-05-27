<?php

namespace App\Http\Livewire;

use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Events\ReservationWarningEvent;
use App\Models\Extra;
use App\Models\Point;
use App\Models\Reservation;
use App\Models\Route;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use WireUi\Traits\Actions;
use Illuminate\Support\Facades\Auth;


class EditTransferReservation extends Component
{
use Actions;
    public Reservation $reservation;
    public $sendModifyMail = 1;
    public $guest_pick_up = '';

    public $date;
    public $adults;
    public $infants;
    public $children;
    public $luggage;
    public $flight_number;
    public $remark;
    public $available_extras;
    public $reservation_extras = array();
    public $is_out_transfer = false;

    public $test = false;

    protected $listeners = ['refreshEditTransfer' => 'refresh'];

    public function getSelectedExtrasProperty()
    {
        return Extra::with(['partner' => function ($q) {
            $q->where('id', $this->reservation->partner_id);
        }])->findMany(collect($this->getActiveReservationExtras())->reject(function ($item) {
            return $item === false;
        })->keys()->toArray());

    }

    public function mount($reservation)
    {

        $this->reservation = $reservation;
        $this->date = $this->reservation->date_time->format('d.m.Y H:i');

        $this->adults = $this->reservation->adults;
        $this->infants = $this->reservation->infants;
        $this->children = $this->reservation->children;
        $this->luggage = $this->reservation->luggage;
        $this->flight_number = $this->reservation->flight_number;
        $this->guest_pick_up = Carbon::parse($this->reservation->flight_pickup_time)->format('d.m.Y H:i');

        $this->remark = $this->reservation->remark;

        $start_point = Point::findOrFail($this->reservation->pickup_address_id);

        if($this->reservation->is_main != 1){
            $start_point = Point::findOrFail($this->reservation->dropoff_address_id);
        }

        if($start_point->type == "accommodation"){
            $this->is_out_transfer = true;
        }

        $this->loadPartnerExtras();

        if(!empty($this->available_extras)){
            foreach($this->available_extras as $extra_id => $extra){
                $this->reservation_extras[$extra->id] = $this->reservation->getExtrasQuantity($extra->id);
            }
        }
    }

    protected function rules()
    {
        $rules = [
            'date' => 'required|date|after_or_equal:' . Carbon::now()->format('d.m.Y') ,
            'reservation.adults' => 'required|numeric|integer',
            'reservation.children' => 'required|numeric|integer',
            'reservation.infants' => 'required|numeric|integer',
            'reservation.luggage' => 'required|numeric|integer',
            'reservation.remark' => 'nullable|string',
            'reservation.flight_number' => 'nullable|string',
        ];

        if ($this->reservation->isDirty('dateTime')) {
            $rules['date'] = 'required|date|after_or_equal:' . Carbon::now()->format('d.m.Y') ;
        }

        return $rules;
    }

    public function updatedDate() {

        $this->reservation->date_time = Carbon::createFromFormat('d.m.Y H:i',$this->date);

    }

    public $fieldNames = [
        'reservation.date_time' => 'date & time',
        'reservation.time' => 'time',
        'reservation.adults' => 'adults',
        'reservation.children' => 'children',
        'reservation.infants' => 'infants',
        'reservation.luggage' => 'luggage',
        'reservation.remark' => 'remark',
        'reservation.flight_number' => 'remark',
    ];



    public function cancel()
    {
        $this->emit('updateCancelled');
    }

    public function confirmationDialog(){

        $this->dialog()->confirm([
            'title'       => 'You are about to modify a reservation?',
            'description' => 'Proceed with the modification?',
            'icon'        => 'question',
            'accept'      => [
                'label'  => 'Yes, modify',
                'method' => 'save',
            ],
            'reject' => [
                'label'  => 'No, cancel',
                'method' => 'cancel',
            ],
        ]);


    }

    public function updatedReservationExtras($value, $key)
    {
        $this->reservation_extras[$key] = $value;
    }

    public function save(): void
    {

        $this->validate($this->rules(),[],$this->fieldNames);

        $filter_extras = array();

        if(!empty($this->getExtrasConfiguration())){

            $selectedExt = $this->getExtrasConfiguration();

            $filter_extras = $this->available_extras;

            if(!empty($filter_extras)){
                foreach($filter_extras as $index => $extra){
                    if(empty($selectedExt[$extra->id]) || $selectedExt[$extra->id] < 1){
                        unset($filter_extras[$index]);
                    }
                }
            }

        }


        $modify = array();

        $res_time = substr($this->reservation->date_time->toTimeString(),0,5);
        $res_date = substr($this->reservation->date_time->toDateTimeString(),0,10);
        $save_date = \Carbon\Carbon::parse(substr($this->date,0,10))->format('Y-m-d');
        $save_time = substr($this->date,11,5);

        $guest_pickup_time_difference = false;

        if($this->is_out_transfer){
            $res_flight_pickup_time = $this->reservation->flight_pickup_time;
            $save_flight_pickup_time =  \Carbon\Carbon::parse(substr($this->guest_pick_up,0,10))->format('Y-m-d').' '.substr($this->guest_pick_up,11,5).':00';

            if($res_flight_pickup_time != $save_flight_pickup_time){
                $this->reservation->flight_pickup_time = $save_flight_pickup_time;
                $guest_pickup_time_difference = true;
            }
        }

        $extras_difference = false;
        #Check if some of the keys are removed
        if(!empty($this->reservation->extras()->get()->keyBy('id')->toArray())){
            foreach($this->reservation->extras()->get()->keyBy('id')->toArray() as $extra){

                if(!in_array($extra['id'],array_keys($this->getActiveReservationExtras()))){
                    $extras_difference = true;
                    break;
                }
            }
        }else{
            if(!empty($this->getActiveReservationExtras())){
                $extras_difference = true;

            }
        }

        #Check if something is added in comparison to previous bookings
        if(!$extras_difference){
            if(!empty($this->getActiveReservationExtras())){
                foreach($this->getActiveReservationExtras() as $extra_id => $data){

                    if(!in_array($extra_id,array_keys($this->reservation->extras()->get()->keyBy('id')->toArray()))){
                        $extras_difference = true;
                        break;
                    }
                }
            }
        }

        if($extras_difference){
            $modify[] = 'extras';
        }

        if($res_date != $save_date){
            $modify[] = 'date';
        }

        if($res_time != $save_time){
            $modify[] = 'time';
        }

        if($guest_pickup_time_difference){
            $modify[] = 'guest_pickup_time';
        }

        if($this->adults != $this->reservation->adults){
            $modify[] = 'adults';
            $this->reservation->adults = $this->adults;
        }

        if($this->children != $this->reservation->children){
            $modify[] = 'children';
            $this->reservation->children = $this->children;
        }

        if($this->infants != $this->reservation->infants){
            $modify[] = 'infants';
            $this->reservation->infants = $this->infants;

        }

        if($this->flight_number != $this->reservation->flight_number){
            $modify[] = 'flight_number';
            $this->reservation->flight_number = $this->flight_number;
        }

        if($this->remark != $this->reservation->remark){
            $modify[] = 'remark';
            $this->reservation->remark = $this->remark;
        }

        if($this->luggage != $this->reservation->luggage){
            $modify[] = 'luggage';
            $this->reservation->luggage = $this->luggage;
        }

        if(!empty($modify)){

            $array = array();

            $array['reservation_id'] = $this->reservation->id;

            foreach($modify as $mod){
                $array[$mod] = 1;
            }
            $array['sent'] = 0;
            $array['updated_by'] = Auth::id();
            \DB::table('reservation_modification')->insert(
                $array
            );
        }


        $this->reservation->date_time = $this->date;

        $extras_difference = true;

        if($extras_difference){

            $route = Route::where('starting_point_id', $this->reservation->pickup_location)
                ->where('ending_point_id', $this->reservation->dropoff_location)
                ->first();


            if($this->reservation->is_main == 1){
                $is_rt = $this->reservation->isRoundTrip() ? 1 : 0;
            }else{
                $main_reservation =  \App\Models\Reservation::where('round_trip_id',$this->reservation->id)->get()->first();

                $is_rt = $main_reservation->isRoundTrip() ? 1 : 0;
            }

            $priceHandler = (new \App\Services\TransferPriceCalculator($this->reservation->transfer_id,
                $this->reservation->partner_id,
                $is_rt,
                $route ? $route->id : null,
                $this->getExtrasConfiguration()))
                ->setBreakdownLang($this->reservation->confirmation_language);

        
            #Delete Previously Saved
            DB::table('extra_reservation')->where('reservation_id',$this->reservation->id)->delete();

            #Add To New
            $this->reservation->extras()->saveMany($filter_extras);

            $this->reservation->price_breakdown = $priceHandler->getPriceBreakdown();
            $this->reservation->price = $priceHandler->getPrice()->getAmount();

            #Recalculate the price
            if($this->reservation->is_main == 1){
                if($this->reservation->isRoundTrip()){

                    #Delete Previously Saved
                    DB::table('extra_reservation')->where('reservation_id',$this->reservation->returnReservation->id)->delete();
                    #Add To New
                    $this->reservation->returnReservation->extras()->saveMany($filter_extras);

                    $route = Route::where('starting_point_id', $this->reservation->returnReservation->pickup_location)
                        ->where('ending_point_id', $this->reservation->returnReservation->dropoff_location)
                        ->first();

                    $priceHandler = (new \App\Services\TransferPriceCalculator($this->reservation->returnReservation->transfer_id,
                        $this->reservation->returnReservation->partner_id,
                        1,
                        $route ? $route->id : null,
                        $this->getExtrasConfiguration()))
                        ->setBreakdownLang($this->reservation->returnReservation->confirmation_language);

                    $this->reservation->returnReservation->price_breakdown = $priceHandler->getPriceBreakdown();
                    $this->reservation->returnReservation->price = $priceHandler->getPrice()->getAmount();

                    $this->reservation->returnReservation->save();
                }
            }else{

                $main_reservation =  \App\Models\Reservation::where('round_trip_id',$this->reservation->id)->get()->first();

                #Delete Previously Saved
                DB::table('extra_reservation')->where('reservation_id',$main_reservation->id)->delete();

                #Add To New
                $main_reservation->extras()->saveMany($filter_extras);

                $route = Route::where('starting_point_id', $main_reservation->pickup_location)
                    ->where('ending_point_id', $main_reservation->dropoff_location)
                    ->first();

                $priceHandler = (new \App\Services\TransferPriceCalculator($main_reservation->transfer_id,
                    $main_reservation->partner_id,
                    $main_reservation->isRoundTrip() ? 1 : 0,
                    $route ? $route->id : null,
                    $this->getExtrasConfiguration()))
                    ->setBreakdownLang($main_reservation->confirmation_language);

                $main_reservation->price_breakdown = $priceHandler->getPriceBreakdown();
                $main_reservation->price = $priceHandler->getPrice()->getAmount();

                $main_reservation->save();

            }
        }


        $updater = new UpdateReservation($this->reservation);
        $updater->setSendMailBool($this->sendModifyMail);

        $updater->updateReservation();

        if($this->sendModifyMail){
            $this->reservation->setModificationsAsSent();
        }

        ##Check if the modification has caused max number of occupants to be bigger than supported occupancy
        if($this->reservation->getNumPassangersAttribute() > $this->reservation->Transfer->Vehicle->max_occ){

            ReservationWarningEvent::dispatch($this->reservation,[
                ReservationWarningEvent::SEND_MAIL_CONFIG_PARAM => true,
            ]);
        }


        $this->emit('updateCompleted');
    }

    private function loadPartnerExtras(){

        $this->available_extras = Extra::with(['partner' => function ($q) {
                    $q->where('id', $this->reservation->partner_id);
                }])->get();

        ##Load Only Extras that are included in reservation
        foreach($this->available_extras as $index => $extra){
            #Remove Paid Extras
            if($extra->partner->first()?->pivot->price > 0){
              //  unset($this->available_extras[$index]);
            }
        }

        #Fill Previously Chosen Data
        if(!empty($this->reservation->extras()->get()->toArray())){
            foreach($this->reservation->extras()->get()->toArray() as $extra){
                $this->reservation_extras[$extra['id']] = !empty($this->reservation->getExtrasQuantity($extra['id'])) ? $this->reservation->getExtrasQuantity($extra['id']) : 0;
            }
        }


    }

    private function getActiveReservationExtras(){

        $return = array();

        if(!empty($this->reservation_extras)){
            foreach($this->reservation_extras as $k => $v){
                if($v === true){
                    $return[$k] = true;
                }
            }
        }

        return $return;
    }

    public function getExtrasConfiguration(){

        $extras = array();

        if(!empty($this->reservation_extras)){
            foreach($this->reservation_extras as $extra_id => $quantity){
                if($quantity > 0){
                    $extras[$extra_id] = $quantity;
                }
            }
        }

        return $extras;
    }

    public function refresh()
    {
        // Forcefully re-fetch the reservation or re-initialize as needed
        $this->reservation = Reservation::findOrFail($this->reservation->id);
        $this->render();
    }

    public function render()
    {
        return view('livewire.edit-transfer-reservation');
    }
}
