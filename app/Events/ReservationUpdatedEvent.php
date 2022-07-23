<?php

namespace App\Events;

use App\Models\Reservation;
use Illuminate\Foundation\Events\Dispatchable;

class ReservationUpdatedEvent
{
    use Dispatchable;

    const SEND_MAIL_CONFIG_PARAM = 'send_mail';

    public $reservation;
    public $config;


    public function __construct(Reservation $reservation,array $config = [])
    {

        $this->reservation = $reservation;
        $this->config = $config;

    }
}
