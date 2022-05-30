<?php

namespace App\Listeners;

use App\Events\ReservationCancelledEvent;

class SendCancelMailListener
{
    public function __construct()
    {
        //
    }

    public function handle(ReservationCancelledEvent $event)
    {

    }
}
