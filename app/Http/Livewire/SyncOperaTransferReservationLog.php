<?php

namespace App\Http\Livewire;

use App\BusinessModels\Reservation\Actions\CancelReservation;
use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Models\Reservation;
use App\Services\Api\ValamarOperaApi;
use Livewire\Component;
use WireUi\Traits\Actions;

class SyncOperaTransferReservationLog extends Component
{
    use Actions;
    public Reservation $reservation;
    public array $log;

    public bool $cancelRoundTrip = true;

    public function close()
    {
        $this->emit('syncLogClosed');
    }

    public function syncReservation(){
        if($this->reservation->is_main){
            $api = new ValamarOperaApi();
            $api->syncReservationWithOpera($this->reservation->id);
            $this->notification()->success('Sync Completed');
            $this->emit('syncCompleted');
        }
    }

    private function getOperaSyncLog(){

        $log = array();

        if($this->reservation->is_main){
            $this->log  = ValamarOperaApi::getSyncOperaLog($this->reservation->id);
        }
    }

    public function openOperaSyncLogModal($log_id){

        $this->log =  ValamarOperaApi::getSyncOperaLog($this->reservation->id,$log_id);

        $log['request'] = json_decode($this->log[$log_id]->opera_request,true);
        $log['response'] = json_decode($this->log[$log_id]->opera_response,true);

        dd($log);
    }

    public function render()
    {
        $this->getOperaSyncLog();
        return view('livewire.sync-opera-transfer-reservation-log');
    }
}
