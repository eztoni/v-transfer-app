<?php

namespace App\Http\Livewire;

use App\Models\Reservation;
use Livewire\Component;

class ReservationDetails extends Component
{

    public Reservation $reservation;
    public bool $openEditModal = false;

    public Reservation|null $editReservation = null;

    protected $listeners = [
        'updateCancelled' => 'closeUpdateModal',
        'updateCompleted' => 'closeUpdateModal'
    ];




    public function mount()
    {
        if (!$this->reservation->is_main) {
            $this->redirect(route('reservation-details', $this->reservation->round_trip_id));
        }
    }

    public function openUpdateModal($id)
    {
        $this->editReservation = Reservation::findOrFail($id);
        $this->openEditModal = true;
    }
    public function closeUpdateModal()
    {
        $this->editReservation = null;
        $this->openEditModal = false;
    }

    public function render()
    {
        return view('livewire.reservation-details');
    }
}
