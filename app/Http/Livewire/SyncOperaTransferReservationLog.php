<?php

namespace App\Http\Livewire;

use App\BusinessModels\Reservation\Actions\CancelReservation;
use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Models\Reservation;
use App\Services\Api\ValamarOperaApi;
use App\Services\Api\ValamarAlertApi;
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
            $api->syncReservationWithOperaFull($this->reservation->id);
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

        #Mask US
        if(!empty($log['request'] [\App\Services\Api\ValamarAlertApi::FIELD_SYS_USER])){
            $log['request'] [\App\Services\Api\ValamarAlertApi::FIELD_SYS_USER] = substr($log['request'] [\App\Services\Api\ValamarAlertApi::FIELD_SYS_USER],0,2).str_repeat('*',strlen($log['request'] [\App\Services\Api\ValamarAlertApi::FIELD_SYS_USER])-2);
        }

        #Mask PW
        if(!empty($log['request'] [\App\Services\Api\ValamarAlertApi::FIELD_SYS_PASS])){
            $log['request'] [\App\Services\Api\ValamarAlertApi::FIELD_SYS_PASS] = substr($log['request'] [\App\Services\Api\ValamarAlertApi::FIELD_SYS_PASS],0,2).str_repeat('*',strlen($log['request'] [\App\Services\Api\ValamarAlertApi::FIELD_SYS_PASS])-2);
        }

        dd($log);
    }

    public function render()
    {
        $this->getOperaSyncLog();
        return view('livewire.sync-opera-transfer-reservation-log');
    }
}
