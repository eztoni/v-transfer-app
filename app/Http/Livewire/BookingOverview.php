<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Partner;
use App\Models\Reservation;
use Livewire\Component;

class BookingOverview extends Component
{

    public $destinationId;
    public $partnerId;
    public $dateRange;


    public function getReservationsProperty(){
        return Reservation::with(['leadTraveller','pickupLocation'])->get();
    }


    public function render()
    {
        $partners = Partner::all();
        $destinations = Destination::all();
        return view('livewire.booking-overview',compact('partners','destinations'));
    }
}
