<?php

namespace App\Http\Livewire;


use Livewire\Component;

class ReservationView extends Component
{

    public $reservation;

    public function render()
    {
        return view('livewire.reservation-view');
    }
}
