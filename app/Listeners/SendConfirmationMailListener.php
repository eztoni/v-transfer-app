<?php

namespace App\Listeners;

use App\Events\ReservationCreatedEvent;

class SendConfirmationMailListener
{
    public function __construct()
    {
        //
    }

    public function handle(ReservationCreatedEvent $event)
    {

    }
}
