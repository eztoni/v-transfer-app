<?php

namespace App\Listeners\Email;

use App\Events\ReservationCreatedEvent;
use App\Mail\Partner\ReservationConfirmationMail;
use Illuminate\Support\Facades\Mail;

class SendConfirmationMailToPartnerListener
{
    // Email addresses who will receive the email
    public array $emailList = [];

    public function sendConfirmationMail($resId): void
    {
        Mail::to($this->emailList)->locale('hr')->send(new ReservationConfirmationMail($resId));
    }

    public function handle(ReservationCreatedEvent $event): void
    {
        if( $event->shouldSendMail() ){

            if($partnerEmail = $event->reservation->partner?->email){
                $this->emailList['partnerMail'] = $partnerEmail;
            }

            if($this->emailList){
                $this->sendConfirmationMail($event->reservation->id);
            }
        }

    }
}
