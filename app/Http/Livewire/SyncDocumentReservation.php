<?php

namespace App\Http\Livewire;

use App\BusinessModels\Reservation\Actions\CancelReservation;
use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Models\Reservation;
use App\Services\Api\ValamarFiskalizacija;
use App\Services\Api\ValamarOperaApi;
use Livewire\Component;
use WireUi\Traits\Actions;

class SyncDocumentReservation extends Component
{
    use Actions;
    public Reservation $reservation;

    public bool $cancelRoundTrip = true;

    public function close()
    {
        $this->emit('cancelSyncDocument');
    }

    public function syncReservation(){

        if($this->reservation->is_main){

            $api = new ValamarFiskalizacija($this->reservation->id);

            $response = $api->syncDocument();

            $this->reservation->refresh();

            if($this->reservation->isDocumentConnectedSync()){
                $this->notification()->success('Sync Completed');
                $this->emit('syncDocumentCompleted');
            }else{

                $message = 'Unable to sync the document with Opera';

                if(!empty($response)){
                    $message = $response;
                }

                $this->notification()->error($message);
            }
        }
    }

    public function render()
    {
        return view('livewire.sync-document-reservation');
    }
}
