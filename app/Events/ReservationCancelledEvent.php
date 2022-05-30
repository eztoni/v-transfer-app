<?php

namespace App\Events;

use App\Models\Reservation;
use Illuminate\Foundation\Events\Dispatchable;

class ReservationCancelledEvent
{
    use Dispatchable;

    public $reservation;


    public function __construct(Reservation $reservation)
    {

        $this->reservation = $reservation;

    }
}
