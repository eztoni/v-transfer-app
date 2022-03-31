<?php

namespace App\Http\Livewire;

use App\Models\Transfer;
use Livewire\Component;

class InternalReservation extends Component
{

    public $stepOneFields = [
        'destinationId'=>null,
        'pickupPointId'=>null,
        'dropOffPointId'=>null,
    ];


    public function getTransfersProperty(){
        return Transfer::with('media')->get();
    }


    public function render()
    {
        return view('livewire.internal-reservation');
    }
}
