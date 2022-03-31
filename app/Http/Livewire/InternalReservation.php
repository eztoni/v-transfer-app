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

    public $step = 1;

    public $travellers = [1];

    public function addTraveller()
    {
        $this->travellers[]=1;
    }

    public function getTransfersProperty(){
        return Transfer::with('media')->get();
    }

    public function selectTransfer(){
        $this->step=2;
    }

    public function render()
    {
        return view('livewire.internal-reservation');
    }
}
