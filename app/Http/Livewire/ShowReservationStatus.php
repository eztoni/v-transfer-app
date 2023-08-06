<?php

namespace App\Http\Livewire;

use App\BusinessModels\Reservation\Actions\CancelReservation;
use App\BusinessModels\Reservation\Actions\UpdateReservation;
use App\Facades\EzMoney;
use App\Models\Partner;
use App\Models\Reservation;
use App\Services\Api\ValamarFiskalizacija;
use App\Services\Api\ValamarOperaApi;
use Carbon\Carbon;
use http\Env\Request;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use WireUi\Traits\Actions;
use function Symfony\Component\String\b;

class ShowReservationStatus extends Component
{
use Actions;

    public Reservation $reservation;

    public function close()
    {
        Redirect::route('bookings');
    }

    public function mount(){

    }


    public function updated($property){

    }

    public function newBooking(){
        Redirect::route('internal-reservation', $this->reservation->id);
    }

    public function showReservation(){
        Redirect::route('reservation-details', $this->reservation->id);
    }

    public function render()
    {
        return view('livewire.show-reservation-status');
    }
}
