<?php

namespace App\Http\Livewire;

use App\Models\Reservation;
use App\Services\Api\ValamarOperaApi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use WireUi\Traits\Actions;

class ReservationDetails extends Component
{
use Actions;

    public Reservation|null $reservation;

    public Reservation|null $editReservation = null;
    public Reservation|null $cancelReservation = null;
    public bool $cancelModal = false;
    public bool $editModal = false;
    public bool $operaSyncModal = false;
    public bool $operaSyncLogModal = false;
    public bool $fiskalSyncModal = false;

    public array $syncLog;

    public $rules = [
        'editReservations'=>'nullable',
        'cancelReservation'=>'nullable',
    ];


    protected $listeners = [
        'updateCancelled' => 'closeUpdateModal',
        'updateCompleted' => 'updateCompleted',
        'cancelCancelled' => 'closeCancelModal',
        'cancelCompleted' => 'cancelCompleted',
        'syncCompleted'   => 'closeSyncModal',
        'syncCancelled'   => 'closeSyncModal',
        'syncLogClosed'   => 'closeSyncLogModal',
        'cancelIssueInvoice' => 'closeIssueInvoice',
        'invoiceIssueCompleted' => 'closeIssueInvoice'
    ];

    public function mount()
    {
        if (!$this->reservation->is_main) {
            $this->redirect(route('reservation-details', $this->reservation->round_trip_id));
        }
    }
    public function openCancelModal($id)
    {
        $this->cancelModal= true;

        $this->cancelReservation = Reservation::findOrFail($id);

    }

    public function cancelCompleted(){

        $this->redirect(route('reservation-details', $this->reservation->id));
    }

    public function closeCancelModal()
    {
        $this->cancelModal= false;
        $this->cancelReservation = null;
        $this->render();
    }

    public function closeSyncModal(){
        $this->operaSyncModal = false;
        $this->render();
    }
    public function closeSyncLogModal(){
        $this->operaSyncLogModal = false;
        $this->render();
    }

    public function closeIssueInvoice(){
        $this->fiskalSyncModal = false;
        $this->render();
    }

    public function openUpdateModal($id)
    {
        $this->editModal = true;

        $this->editReservation = Reservation::findOrFail($id);
    }

    public function openOperaSyncModal($id){
        $this->operaSyncModal = true;
        $this->reservation = Reservation::findOrFail($id);
    }

    public function openFiskalSyncModal($id){
        $this->fiskalSyncModal = true;
        $this->reservation = Reservation::findOrFail($id);
    }

    public function openOperaSyncLogModal($id){
        $this->operaSyncLogModal = true;
        $this->syncLog = ValamarOperaApi::getSyncOperaLog($id);
    }

    public function updateCompleted(){
        $this->closeUpdateModal();
        $this->notification()->success('Reservation updated');
    }

    public function closeUpdateModal()
    {
        $this->editModal = false;
        $this->editReservation = null;
        $this->render();

    }

    public function downloadConfirmationPDF($id){
        $this->redirect('/download_document/booking-confirmation/'.$id);
    }

    public function downloadCancellationPDF($id){
        $this->redirect('/download_document/booking-cancellation/'.$id);
    }

    public function downloadCFPDF($id){

        $reservation = Reservation::findOrFail($id);

        $this->redirect('/download_document/booking-cancellation-fee/'.$id);
    }


    public function render()
    {
        return view('livewire.reservation-details');
    }
}
