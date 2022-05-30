<?php

namespace App\Http\Livewire;

use App\BusinessModels\Reservation\Actions\CancelReservation;
use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Models\Reservation;
use Livewire\Component;

class CancelTransferReservation extends Component
{
    public Reservation $reservation;

    public bool $cancelRoundTrip = false;

    public function close()
    {
        $this->emit('cancelCancelled');
    }

    public function cancelReservation()
    {
        $cancelAction = new CancelReservation($this->reservation);

        $cancelAction->cancelReservation();
        if($this->cancelRoundTrip){

            $cancelAction->cancelRoundTrip();
        }

        $this->emit('cancelCompleted');
    }



    public function render()
    {
        return view('livewire.cancel-transfer-reservation');
    }
}
