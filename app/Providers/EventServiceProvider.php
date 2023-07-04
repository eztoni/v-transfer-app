<?php

namespace App\Providers;

use App\Events\ReservationAlertEvent;
use App\Events\ReservationCancelledEvent;
use App\Events\ReservationCreatedEvent;
use App\Events\ReservationUpdatedEvent;
use App\Events\ReservationWarningEvent;
use App\Listeners\Email\SendAlertMailToVEC;
use App\Listeners\Email\SendCancellationMailToGuest;
use App\Listeners\Email\SendCancellationMailToGuestListener;
use App\Listeners\Email\SendCancellationMailToPartnerListener;
use App\Listeners\Email\SendConfirmationMailToGuestListener;
use App\Listeners\Email\SendConfirmationMailToPartnerListener;
use App\Listeners\Email\SendModificationMailToGuestListener;
use App\Listeners\Email\SendModificationMailToPartnerListener;
use App\Listeners\Email\SendWarningMailToVEC;
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
            SendConfirmationMailToPartnerListener::class,
            SendConfirmationMailToGuestListener::class
        ],

        ReservationUpdatedEvent::class => [
            SendModificationMailToPartnerListener::class,
            SendModificationMailToGuestListener::class,
        ],

        ReservationCancelledEvent::class => [
            SendCancellationMailToPartnerListener::class,
            SendCancellationMailToGuestListener::class,
        ],
        ReservationWarningEvent::class => [
          SendWarningMailToVEC::class,
        ],
        ReservationAlertEvent::class => [
          SendAlertMailToVec::class,
        ],
    ];


}
