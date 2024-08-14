<?php

namespace App\Http\Livewire;

use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Events\ReservationWarningEvent;
use App\Models\Reservation;
use Carbon\Carbon;
use Livewire\Component;
use WireUi\Traits\Actions;
use Illuminate\Support\Facades\Auth;


class EditTransferReservation extends Component
{
use Actions;
    public Reservation $reservation;
    public $sendModifyMail = 1;

    public $date;
    public $adults;
    public $infants;
    public $children;
    public $luggage;
    public $flight_number;
    public $remark;

    public function mount()
    {
        $this->date = $this->reservation->date_time->format('d.m.Y H:i');

        $this->adults = $this->reservation->adults;
        $this->infants = $this->reservation->infants;
        $this->children = $this->reservation->children;
        $this->luggage = $this->reservation->luggage;
        $this->flight_number = $this->reservation->flight_number;
        $this->remark = $this->reservation->remark;

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


    public function save(): void
    {

        $this->validate($this->rules(),[],$this->fieldNames);

        $modify = array();

        $res_time = substr($this->reservation->date_time->toTimeString(),0,5);
        $res_date = substr($this->reservation->date_time->toDateTimeString(),0,10);
        $save_date = \Carbon\Carbon::parse(substr($this->date,0,10))->format('Y-m-d');
        $save_time = substr($this->date,11,5);


        if($res_date != $save_date){
            $modify[] = 'date';
        }

        if($res_time != $save_time){
            $modify[] = 'time';
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


    public function render()
    {
        return view('livewire.edit-transfer-reservation');
    }
}
