<?php

namespace App\Listeners\Email;

use App\Events\ReservationUpdatedEvent;
use App\Mail\Partner\ReservationModificationMail;
use Illuminate\Support\Facades\Mail;

class SendModificationMailToPartnerListener
{

    public array $emailList = array();


    public function sendConfirmationMail($resId){
        Mail::to($this->emailList)->locale('hr')->send(new ReservationModificationMail($resId));
    }

    public function handle(ReservationUpdatedEvent $event)
    {
        if( $event->shouldSendMail()){
            $reservation = $event->reservation;

            if($partnerEmail = $event->reservation->partner?->email){
                $this->emailList['partnerMail'] = $partnerEmail;
            }

            if($this->emailList){
                $this->sendConfirmationMail($reservation->id);
            }
        }
    }
}
