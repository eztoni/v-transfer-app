<?php

namespace App\Http\Livewire;

use App\Models\Reservation;
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

    public $rules = [
        'editReservations'=>'nullable',
        'cancelReservation'=>'nullable',
    ];

    protected $listeners = [
        'updateCancelled' => 'closeUpdateModal',
        'updateCompleted' => 'updateCompleted',
        'cancelCancelled' => 'closeCancelModal',
        'cancelCompleted' => 'cancelComplete'
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

    public function cancelComplete(){
        $this->redirect(route('reservation-details', $this->reservation->id));
    }

    public function closeCancelModal()
    {
        $this->cancelModal= false;
        $this->cancelReservation = null;
        $this->render();
    }

    public function openUpdateModal($id)
    {
        $this->editModal = true;

        $this->editReservation = Reservation::findOrFail($id);
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

    public function render()
    {
        return view('livewire.reservation-details');
    }
}
