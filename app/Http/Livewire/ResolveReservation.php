<?php

namespace App\Http\Livewire;
use Carbon\Carbon;
use App\Services\Api\ValamarOperaApi;
use Livewire\Component;
use WireUi\Traits\Actions;
use App\Models\Reservation;

class ResolveReservation extends Component
{
    use Actions;

    public Reservation $reservation;
    public $comment = '';

    public function close()
    {
        $this->emit('resolveClosed');
    }

    public function resolveReservation(){

        $this->reservation->resolved = 1;
        $this->reservation->resolve_comment = $this->comment;
        $this->reservation->resolved_by = auth()->user()->id;
        $this->reservation->resolved_at = Carbon::now()->addHour()->format('Y-m-d H:i:s');

        $this->reservation->save();

        $this->emit('reservationResolved');

    }

    public function render()
    {
        return view('livewire.resolve-reservation');
    }
}
