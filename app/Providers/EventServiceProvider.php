<?php

namespace App\Providers;

use App\Events\ReservationCancelledEvent;
use App\Events\ReservationCreatedEvent;
use App\Events\ReservationUpdatedEvent;
use App\Listeners\Email\SendCancellationMailToGuest;
use App\Listeners\Email\SendCancellationMailToGuestListener;
use App\Listeners\Email\SendConfirmationMailToGuestListener;
use App\Listeners\Email\SendModificationMailToGuestListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ReservationCreatedEvent::class => [

            SendConfirmationMailToGuestListener::class,

        ],

        ReservationUpdatedEvent::class => [

            SendModificationMailToGuestListener::class,

        ],

        ReservationCancelledEvent::class => [

            SendCancellationMailToGuestListener::class,

        ],
    ];


}
