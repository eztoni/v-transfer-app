<?php

namespace App\Http\Livewire;

use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Models\Reservation;
use Livewire\Component;

class CancelTransferReservation extends Component
{
    public Reservation $reservation;


    public function close()
    {
        $this->emit('cancelCancelled');
    }

    public function cancelReservation()
    {


        $this->emit('cancelCompleted');
    }



    public function render()
    {
        return view('livewire.cancel-transfer-reservation');
    }
}
