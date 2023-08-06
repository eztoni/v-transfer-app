<?php

namespace App\Listeners\Email;

use App\Events\ReservationCreatedEvent;
use App\Mail\Partner\ReservationConfirmationMail;
use App\Models\Reservationmail;
use Illuminate\Support\Facades\Mail;

class SendConfirmationMailToPartnerListener
{
    // Email addresses who will receive the email
    public array $emailList = [];

    public function sendConfirmationMail($resId): void
    {
        $return = Mail::to($this->emailList)->locale('hr')->send(new ReservationConfirmationMail($resId));


        if($return){
            $log = new Reservationmail();

            $log->reservation_id = $resId;
            $log->from = json_encode(array(env('MAIL_FROM_ADDRESS')));
            $log->to = json_encode(array_values($this->emailList));
            $log->email_type = 'partner_confirmation';
            $log->debug_log = $return->getDebug();

            $log->save();
        }

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
