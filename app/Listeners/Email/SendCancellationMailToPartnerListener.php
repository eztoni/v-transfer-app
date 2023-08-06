<?php

namespace App\Listeners\Email;

use App\Events\ReservationCancelledEvent;
use App\Mail\Partner\ReservationCancellationMail;
use App\Models\Reservationmail;
use Illuminate\Support\Facades\Mail;

class SendCancellationMailToPartnerListener
{
    public array $emailList = array();

    public function sendMail($resId): void
    {
        $mail = new ReservationCancellationMail($resId);


        $return = Mail::to($this->emailList)->locale('hr')->send($mail);

        if(!empty($return)){

            $log = new Reservationmail();

            $log->reservation_id = $resId;
            $log->from = json_encode(array(env('MAIL_FROM_ADDRESS')));
            $log->to = json_encode(array_values($this->emailList));
            $log->email_type = 'partner_cancellation';
            $log->debug_log = $return->getDebug();

            $log->save();
        }


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
