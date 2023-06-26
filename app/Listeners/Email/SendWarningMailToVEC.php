<?php

namespace App\Listeners\Email;

use App\Events\ReservationWarningEvent;
use App\Mail\Partner\ReservationConfirmationMail;
use App\Mail\Partner\ReservationWarningMail;
use Illuminate\Support\Facades\Mail;

class SendWarningMailToVEC
{
    // Email addresses who will receive the email
    public array $emailList = [];
    public $vec_mail = 'transfer@valamar.com';

    public function sendWarningEmail($resId): void
    {
        Mail::to($this->emailList)->locale('hr')->send(new ReservationWarningMail($resId));
    }

    public function handle(ReservationWarningEvent $event): void
    {

        if( $event->shouldSendMail() ){

            $this->emailList['vec_email'] = $this->vec_mail;

            if($this->emailList){
                $this->sendWarningEmail($event->reservation->id);
            }
        }

    }
}
