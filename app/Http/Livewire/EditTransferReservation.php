<?php

namespace App\Http\Livewire;

use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use App\Mail\ModificationMail;
use WireUi\Traits\Actions;

class EditTransferReservation extends Component
{
use Actions;
    public Reservation $reservation;
    public $sendModifyMail = 1;
    public $emailList = array();
    public $sendEmailArray = [
        0 => 'No',
        1 => 'Yes',
    ];

    public $date;

    public function mount()
    {
        $this->date = $this->reservation->date_time->format('d.m.Y H:i');
    }


    protected function rules()
    {
        $rules = [
            'date' => 'required|date|after_or_equal:' . Carbon::now()->format('d.m.Y') ,
            'reservation.adults' => 'required|integer|integer|min:1|max:50',
            'reservation.children' => 'required|numeric|integer|max:50',
            'reservation.infants' => 'required|numeric|integer|max:50',
            'reservation.luggage' => 'required|numeric|integer|max:50',
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

    public function updated($property)
    {
        $this->validateOnly($property,$this->rules(),[],$this->fieldNames);
    }

    public function cancel()
    {
        $this->emit('updateCancelled');
    }

    public function sendModificationMail($userEmails = array(),$resId){
        if($userEmails){
            Mail::to($userEmails)->send(new ModificationMail($resId));
        }
    }

    public function save()
    {

        $this->validate($this->rules(),[],$this->fieldNames);
        $updater = new UpdateReservation($this->reservation);

        $updater->updateReservation();

        if($this->sendModifyMail == 1){


            $travellerMail = $this->reservation->leadTraveller?->email;
            if($travellerMail){
                $this->emailList = \Arr::add($this->emailList, 'travellerMail', $travellerMail);
            }

            $this->sendModificationMail($this->emailList,$this->reservation->id);

        }


        $this->emit('updateCompleted');
    }


    public function render()
    {
        return view('livewire.edit-transfer-reservation');
    }
}
