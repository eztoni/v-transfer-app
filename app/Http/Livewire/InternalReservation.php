<?php

namespace App\Http\Livewire;

use Livewire\Component;

class InternalReservation extends Component
{
    public $destinationId;
    public $pickupPointId;
    public $dropOffPointId;

    public function render()
    {
        return view('livewire.internal-reservation');
    }
}
