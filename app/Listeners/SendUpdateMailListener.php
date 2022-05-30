<?php

namespace App\Listeners;

use App\Events\ReservationUpdatedEvent;

class SendUpdateMailListener
{
    public function __construct()
    {
        //
    }

    public function handle(ReservationUpdatedEvent $event)
    {

    }
}
