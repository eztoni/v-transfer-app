<?php

namespace App\Listeners\Email;

use App\Events\ReservationCancelledEvent;
use App\Mail\Partner\ReservationCancellationMail;
use Illuminate\Support\Facades\Mail;

class SendCancellationMailToPartnerListener
{
    public array $emailList = array();

    public function sendMail($resId): void
    {
        $mail = new ReservationCancellationMail($resId);


        Mail::to($this->emailList)->locale('hr')->send($mail);
    }

    public function handle(ReservationCancelledEvent $event): void
    {
        if( $event->shouldSendMail()){

            $reservation = $event->reservation;

            if($partnerEmail = $reservation->partner?->email){
                $this->emailList['partnerMail'] = $partnerEmail;
            }

            if($this->emailList){
                $this->sendMail($reservation->id);
            }
        }
    }
}
