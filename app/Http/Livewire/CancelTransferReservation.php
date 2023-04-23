<?php

namespace App\Http\Livewire;

use App\BusinessModels\Reservation\Actions\CancelReservation;
use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Models\Reservation;
use Carbon\Carbon;
use http\Env\Request;
use Livewire\Component;
use WireUi\Traits\Actions;

class CancelTransferReservation extends Component
{
use Actions;
    public Reservation $reservation;
    public $cancellationDate;
    public bool $cancelRoundTrip = true;

    public function close()
    {
        $this->emit('cancelCancelled');
    }

    public function cancelReservation()
    {

        if(!$this->cancellationDate){
            $this->cancellationDate = Carbon::now()->format('Y-m-d H:i:ss');
        }

        $cancelAction = new CancelReservation($this->reservation);
        $cancelAction->cancelReservation($this->cancellationDate);

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
