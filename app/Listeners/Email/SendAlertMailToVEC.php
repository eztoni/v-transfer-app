<?php

namespace App\Listeners\Email;

use App\Events\ReservationAlertEvent;
use App\Mail\Partner\ReservationConfirmationMail;
use App\Mail\Partner\ReservationAlertMail;
use Illuminate\Support\Facades\Mail;

class SendAlertMailToVEC
{
    // Email addresses who will receive the email
    public array $emailList = [];

    public $vec_mail = 'lea.heska@valamar.com';

    public $angelica = 'andelika.ritossa@valamar.com';

    public $ivana = 'ivana.fabijancic@valamar.com';

    public function sendAlertEmail($alert_list): void
    {
        Mail::to($this->emailList)->locale('hr')->send(new ReservationAlertMail($alert_list));
    }

    public function handle(ReservationAlertEvent $event): void
    {

        if( $event->shouldSendMail() && !empty($event->alert_list)){

            $this->emailList['vec_email'] = $this->vec_mail;
            $this->emailList['angelica'] = $this->angelica;
            $this->emailList['ivana'] = $this->ivana;

            if($this->emailList){
                $this->sendAlertEmail($event->alert_list);
            }
        }

    }
}
