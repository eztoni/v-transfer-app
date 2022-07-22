<?php

namespace App\Events;

use App\Models\Reservation;
use Illuminate\Foundation\Events\Dispatchable;

class ReservationCreatedEvent
{
    use Dispatchable;

    const SEND_MAIL_CONFIG_PARAM = 'send_mail';

    public $reservation;


    public function __construct(Reservation $reservation,array $config = [])
    {

        $this->reservation = $reservation;

    }
}
