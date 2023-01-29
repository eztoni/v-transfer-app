<?php

namespace App\Listeners\Email;

use App\Events\ReservationCreatedEvent;
use App\Mail\Guest\GuestReservationConfirmationMail;
use Illuminate\Support\Facades\Mail;

class SendConfirmationMailToGuestListener
{
    // Email addresses who will receive the email
    public array $emailList = [];

    public function sendConfirmationMail($userEmails,$resId, $locale = null): void
    {
        Mail::to($userEmails)->locale($locale)->send(new GuestReservationConfirmationMail($resId,$locale??'en'));
    }

    public function handle(ReservationCreatedEvent $event): void
    {
        if( $event->shouldSendMail() ){

            // Add email to email list
            if($travellerMail = $event->reservation->leadTraveller?->email){
                $this->emailList['travellerMail'] = $travellerMail;
            }


            if($this->emailList){
                $this->sendConfirmationMail($this->emailList,$event->reservation->id, $event->reservation->confirmation_language);
            }


        }

    }
}
