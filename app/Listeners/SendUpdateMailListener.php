<?php

namespace App\Listeners;

use App\Events\ReservationCreatedEvent;
use App\Events\ReservationUpdatedEvent;
use App\Mail\ConfirmationMail;
use App\Mail\ModificationMail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class SendUpdateMailListener
{

    public array $emailList = array();

    public function __construct()
    {
        //
    }

    public function sendConfirmationMail($userEmails ,$resId, $locale = null){
        Mail::to($userEmails)->locale($locale??'en')->send(new ModificationMail($resId));
    }

    public function handle(ReservationUpdatedEvent $event)
    {


        if(Arr::get($event->config,ReservationCreatedEvent::SEND_MAIL_CONFIG_PARAM)){

            $reservation = $event->reservation;

            $travellerMail = $reservation->leadTraveller?->email;

            if($travellerMail){
                $this->emailList = \Arr::add($this->emailList, 'travellerMail', $travellerMail);
            }

            if($this->emailList){
                $this->sendConfirmationMail($this->emailList,$reservation->id, $reservation->confirmation_language);
            }
        }
    }
}
