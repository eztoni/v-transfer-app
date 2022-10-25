<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Partner;
use App\Models\Reservation;
use Livewire\Component;
use WireUi\Traits\Actions;

class BookingOverview extends Component
{
use Actions;

    public $destinationId;
    public $partnerId;
    public $dateRange = [
        0=>[],
        1=>[],
    ];


    public function getReservationsProperty(){
        return Reservation::with(['leadTraveller','pickupLocation'])
            ->where('is_main',true)
            ->get();
    }


    public function render()
    {
        $partners = Partner::all();
        $destinations = Destination::all();
        return view('livewire.booking-overview',compact('partners','destinations'));
    }
}
