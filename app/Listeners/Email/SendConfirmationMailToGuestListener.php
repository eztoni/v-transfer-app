<?php

namespace App\Listeners\Email;

use App\Events\ReservationCreatedEvent;
use App\Mail\Guest\ReservationConfirmationMail;
use App\Models\Reservationmail;
use Dompdf\Exception;
use Illuminate\Support\Facades\Mail;

class SendConfirmationMailToGuestListener
{
    // Email addresses who will receive the email
    public array $emailList = [];

    public function sendConfirmationMail($userEmails,$resId, $locale = null): void
    {
            $return = Mail::to($userEmails)->locale($locale)->send(new ReservationConfirmationMail($resId,$locale??'en'));

            if($return){
                $log = new Reservationmail();
                $log->reservation_id = $resId;
                $log->from = json_encode(array(env('MAIL_FROM_ADDRESS')));
                $log->to = json_encode(array_values($userEmails));
                $log->email_type = 'guest_confirmation';
                $log->debug_log = $return->getDebug();

                $log->save();
            }

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
