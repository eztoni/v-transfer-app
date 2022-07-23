<?php

namespace App\Listeners;

use App\Events\ReservationCreatedEvent;
use App\Events\ReservationUpdatedEvent;
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

    public function sendModificationMail($userEmails = array(),$resId){
        if($userEmails){
            Mail::to($userEmails)->send(new ModificationMail($resId));
        }
    }

    public function handle(ReservationUpdatedEvent $event)
    {


        if(Arr::get($event->config,ReservationCreatedEvent::SEND_MAIL_CONFIG_PARAM)){

            $reservation = $event->reservation;

            $travellerMail = $reservation->leadTraveller?->email;
            if($travellerMail){
                $this->emailList = \Arr::add($this->emailList, 'travellerMail', $travellerMail);
            }

            $this->sendModificationMail($this->emailList,$reservation->id);
        }
    }
}
