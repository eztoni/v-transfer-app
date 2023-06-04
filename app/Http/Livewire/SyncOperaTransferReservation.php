<?php

namespace App\Http\Livewire;

use App\BusinessModels\Reservation\Actions\CancelReservation;
use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Models\Reservation;
use App\Services\Api\ValamarOperaApi;
use Livewire\Component;
use WireUi\Traits\Actions;

class SyncOperaTransferReservation extends Component
{
    use Actions;
    public Reservation $reservation;

    public bool $cancelRoundTrip = true;

    public function close()
    {
        $this->emit('syncCancelled');
    }

    public function syncReservation(){


        if($this->reservation->is_main){

            $api = new ValamarOperaApi();

            #$api->syncReservationWithOpera($this->reservation->id);

            if($this->reservation->status =='cancelled'){

                $no_show = $this->reservation->cancellation_type == 'no_show' ? true : false;
                $cancellation_fee = $this->reservation->cancellation_fee;

                if($cancellation_fee > 0){
                    $api->syncReservationCFWithOpera($this->reservation->id,$cancellation_fee,$no_show);
                }

            }

            $this->notification()->success('Sync Completed');
            $this->emit('syncCompleted');
        }
    }

    public function render()
    {
        return view('livewire.sync-opera-transfer-reservation');
    }
}
