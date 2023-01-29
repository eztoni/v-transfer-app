<?php

namespace App\Listeners\Email;

use App\Events\ReservationCancelledEvent;
use App\Events\ReservationUpdatedEvent;
use App\Mail\Guest\GuestReservationCancellationMail;
use App\Mail\Guest\GuestReservationModificationMail;
use Illuminate\Support\Facades\Mail;

class SendCancellationMailToGuestListener
{
    public array $emailList = array();


    public function sendMail($userEmails ,$resId, $locale = null): void
    {
        $mail = new GuestReservationCancellationMail($resId,$locale??'en');


        Mail::to($userEmails)->locale($locale??'en')->send($mail);
    }

    public function handle(ReservationCancelledEvent $event): void
    {
        if( $event->shouldSendMail()){

            $reservation = $event->reservation;


            if($travellerMail = $reservation->leadTraveller?->email){
                $this->emailList = \Arr::add($this->emailList, 'travellerMail', $travellerMail);
            }

            if($this->emailList){
                $this->sendMail($this->emailList,$reservation->id, $reservation->confirmation_language);
            }
        }
    }
}
