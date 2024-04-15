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

            if($receptionEmail = $event->reservation->getReservationReceptionEmail()){
                $this->emailList['receptionMail'] = $receptionEmail;
            }

            #Kontrola Najave
            $this->emailList['safetyCopy'] = 'najava.transferi@valamar.com';

            $acc_id = 0;

            if($event->reservation->pickupAddress->type == 'accommodation'){
                $acc_id = $event->reservation->pickupAddress->id;
            }else{
                $acc_id = $event->reservation->dropoffAddress->id;
            }

            if($acc_id > 0){
                switch($acc_id){
                    #LaCroma
                    case 34:
                        $this->emailList['recPersonalCopy'] = 'ozana.simunovic@imperial.hr';
                        break;
                    #Tirena
                    case 65:
                        $this->emailList['recPersonalCopy'] = 'dragan.stankovic@imperial.hr';
                        break;
                    #Solitudo Camping
                    case 134:
                        $this->emailList['recPersonalCopy'] = 'ivona.camo@valamar.com';
                        break;
                    #Argosy
                    case 18:
                        $this->emailList['recPersonalCopy'] = '	mirko.komnenovic@valamar.com';
                        break;
                    case 17:
                        #President
                        $this->emailList['recPersonalCopy'] = 'jasmina.kneziccumo@valamar.com';
                    break;
                    case 35:
                        #President
                        $this->emailList['recPersonalCopy'] = 'josip.begusic@imperial.hr';
                    break;
                }
            }

            if($this->emailList){
                $this->sendConfirmationMail($event->reservation->id);
            }
        }

    }
}
