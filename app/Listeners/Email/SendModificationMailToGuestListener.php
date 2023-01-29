<?php

namespace App\Listeners\Email;

use App\Events\ReservationCreatedEvent;
use App\Events\ReservationUpdatedEvent;
use App\Mail\Guest\GuestReservationCancellationMail;
use App\Mail\Guest\GuestReservationModificationMail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class SendModificationMailToGuestListener
{

    public array $emailList = array();


    public function sendConfirmationMail($userEmails ,$resId, $locale = null){
        Mail::to($userEmails)->locale($locale??'en')->send(new GuestReservationModificationMail($resId,$locale??'en'));
    }

    public function handle(ReservationUpdatedEvent $event)
    {
        if( $event->shouldSendMail()){

            $reservation = $event->reservation;


            if($travellerMail = $reservation->leadTraveller?->email){
                $this->emailList = \Arr::add($this->emailList, 'travellerMail', $travellerMail);
            }

            if($this->emailList){
                $this->sendConfirmationMail($this->emailList,$reservation->id, $reservation->confirmation_language);
            }
        }
    }
}
