<?php

namespace App\Http\Livewire;

use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Models\Reservation;
use Carbon\Carbon;
use Livewire\Component;

class EditTransferReservation extends Component
{
    public Reservation $reservation;


    protected function rules()
    {
        $rules = [
            'reservation.date' => 'required',
            'reservation.time' => 'required|date_format:H:i',
            'reservation.adults' => 'required|integer|integer|min:1|max:50',
            'reservation.children' => 'required|numeric|integer|max:50',
            'reservation.infants' => 'required|numeric|integer|max:50',
            'reservation.luggage' => 'required|numeric|integer|max:50',
            'reservation.remark' => 'nullable|string',
            'reservation.flight_number' => 'nullable|string',
        ];

        if ($this->reservation->isDirty('date')) {
            $rules['reservation.date'] = 'required|date|after_or_equal:' . Carbon::now()->format('d.m.Y') . '|date_format:d.m.Y';
        }

        return $rules;
    }

    public $fieldNames = [
        'reservation.date' => 'date',
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

    public function save()
    {
        $this->validate($this->rules(),[],$this->fieldNames);

        $updater = new UpdateReservation($this->reservation);

        $updater->updateReservation();


        $this->emit('updateCompleted');
    }


    public function render()
    {
        return view('livewire.edit-transfer-reservation');
    }
}
