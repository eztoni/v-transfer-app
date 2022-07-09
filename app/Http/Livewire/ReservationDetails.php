<?php

namespace App\Http\Livewire;

use App\Models\Reservation;
use Livewire\Component;

class ReservationDetails extends Component
{

    public Reservation|null $reservation;

    public Reservation|null $editReservation = null;
    public Reservation|null $cancelReservation = null;

    public $rules = [
        'editReservations'=>'nullable',
        'cancelReservation'=>'nullable',
    ];

    protected $listeners = [
        'updateCancelled' => 'closeUpdateModal',
        'updateCompleted' => 'updateCompleted',
        'cancelCancelled' => 'closeCancelModal',
        'cancelCompleted' => 'closeCancelModal'
    ];

    public function mount()
    {
        if (!$this->reservation->is_main) {
            $this->redirect(route('reservation-details', $this->reservation->round_trip_id));
        }
    }
    public function openCancelModal($id)
    {
        $this->cancelReservation = Reservation::findOrFail($id);
    }
    public function closeCancelModal()
    {
        $this->cancelReservation = null;
    }

    public function openUpdateModal($id)
    {
        $this->editReservation = Reservation::findOrFail($id);
    }

    public function updateCompleted(){
        $this->closeUpdateModal();
        $this->showToast('Reservation updated');
    }

    public function closeUpdateModal()
    {
        $this->editReservation = null;
    }

    public function render()
    {
        return view('livewire.reservation-details');
    }
}
