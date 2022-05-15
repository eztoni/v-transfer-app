<?php

namespace App\Http\Livewire;

use App\Models\Reservation;
use Livewire\Component;

class ReservationDetails extends Component
{

    public Reservation $reservation;

    public function mount()
    {
        if(!$this->reservation->is_main){
            $this->redirect(route('reservation-details',$this->reservation->round_trip_id));
        }
    }



    public function render()
    {
        return view('livewire.reservation-details');
    }
}
