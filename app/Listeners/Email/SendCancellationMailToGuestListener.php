<?php

namespace App\Listeners\Email;

use App\Events\ReservationCancelledEvent;
use App\Events\ReservationUpdatedEvent;
use App\Mail\Guest\ReservationCancellationMail;
use App\Mail\Guest\ReservationModificationMail;
use App\Models\Reservationmail;
use Illuminate\Support\Facades\Mail;

class SendCancellationMailToGuestListener
{
    public array $emailList = array();


    public function sendMail($userEmails ,$resId, $locale = null): void
    {
        $mail = new ReservationCancellationMail($resId,$locale??'en');


        $return = Mail::to($userEmails)->locale($locale??'en')->send($mail);


        if(!empty($return)){
            $log = new Reservationmail();

            $log->reservation_id = $resId;
            $log->from = json_encode(array(env('MAIL_FROM_ADDRESS')));
            $log->to = json_encode(array_values($userEmails));
            $log->email_type = 'guest_cancellation';
            $log->debug_log = $return->getDebug();

            $log->save();
        }

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
