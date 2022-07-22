<?php

namespace App\Listeners;

use App\Events\ReservationCreatedEvent;
use App\Mail\ConfirmationMail;
use App\Models\Partner;
use App\Models\Point;
use Illuminate\Support\Facades\Mail;

class SendConfirmationMailListener
{

    public array $emailList = array();

    public function __construct()
    {
        //
    }

    public function sendConfirmationMail($userEmails = array(),$resId){
        Mail::to($userEmails)->send(new ConfirmationMail($resId));
    }

    public function handle(ReservationCreatedEvent $event)
    {
        $reservation = $event->reservation;

        $travellerMail = $reservation->leadTraveller->email;
        if($travellerMail){
           $this->emailList = \Arr::add($this->emailList, 'travellerMail', $travellerMail);
        }

        $partnerMail = Partner::findOrFail($reservation->partner->id)->email;
        if($partnerMail) {
            $this->emailList = \Arr::add($this->emailList, 'partnerMail', $partnerMail);
        }

        $accommodationMail = Point::find($reservation->dropoffLocation->id)->reception_email;
        if($accommodationMail){
                $this->emailList = \Arr::add($this->emailList, 'accommodationMail', $accommodationMail);
        }

        if($this->emailList){
             $this->sendConfirmationMail($this->emailList,$reservation->id);
        }

    }
}
