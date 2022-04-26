<?php

namespace App\Http\Livewire;

use Livewire\Component;

class BookingOverview extends Component
{

    public $dateRange;

    public function render()
    {
        return view('livewire.booking-overview');
    }
}
