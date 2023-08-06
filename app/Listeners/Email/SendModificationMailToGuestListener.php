<?php

namespace App\Listeners\Email;

use App\Events\ReservationCreatedEvent;
use App\Events\ReservationUpdatedEvent;
use App\Mail\Guest\ReservationCancellationMail;
use App\Mail\Guest\ReservationModificationMail;
use App\Models\Reservationmail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class SendModificationMailToGuestListener
{

    public array $emailList = array();


    public function sendConfirmationMail($userEmails ,$resId, $locale = null){

        $return = Mail::to($userEmails)->locale($locale??'en')->send(new ReservationModificationMail($resId,$locale??'en'));


        if($return){
            $log = new Reservationmail();

            $log->reservation_id = $resId;
            $log->from = json_encode(array(env('MAIL_FROM_ADDRESS')));
            $log->to = json_encode(array_values($userEmails));
            $log->email_type = 'guest_modification';
            $log->debug_log = $return->getDebug();

            $log->save();
        }

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
