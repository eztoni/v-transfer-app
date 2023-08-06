<?php

namespace App\Listeners\Email;

use App\Events\ReservationUpdatedEvent;
use App\Mail\Partner\ReservationModificationMail;
use App\Models\Reservationmail;
use Illuminate\Support\Facades\Mail;

class SendModificationMailToPartnerListener
{

    public array $emailList = array();


    public function sendConfirmationMail($resId){

        $return = Mail::to($this->emailList)->locale('hr')->send(new ReservationModificationMail($resId));

        if($return){
            $log = new Reservationmail();

            $log->reservation_id = $resId;
            $log->from = json_encode(array(env('MAIL_FROM_ADDRESS')));
            $log->to = json_encode(array_values($this->emailList));
            $log->email_type = 'partner_modification';
            $log->debug_log = $return->getDebug();
            $log->save();
        }
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
