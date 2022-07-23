<?php

namespace App\Http\Livewire;

use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use WireUi\Traits\Actions;
use App\Mail\ModificationMail;
use WireUi\Traits\Actions;

class EditTransferReservation extends Component
{
use Actions;
    public Reservation $reservation;
    public $sendModifyMail = 1;

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

    public function confirmationDialog(){
        $this->dialog()->confirm([
            'title'       => 'You are about to modify a reservation?',
            'description' => 'Proceed with the modification?',
            'icon'        => 'question',
            'accept'      => [
                'label'  => 'Yes, modify',
                'method' => 'save',
                'params' => 'Saved',
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
        $this->reservation->date_time = $this->date;
        $updater = new UpdateReservation($this->reservation);
        $updater->setSendMailBool($this->sendModifyMail);

        $updater->updateReservation();

        $this->emit('updateCompleted');
    }


    public function render()
    {
        return view('livewire.edit-transfer-reservation');
    }
}
