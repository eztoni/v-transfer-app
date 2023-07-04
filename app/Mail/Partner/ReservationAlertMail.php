<?php

namespace App\Mail\Partner;

use App\Models\Reservation;
use App\Models\Traveller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Carbon;

class ReservationAlertMail extends Mailable
{
    public array $alert_list = array();

    public function __construct($alert_list)
    {
        $this->alert_list = $alert_list;

        \App::setLocale('hr');

        $this->subject('Upozorenje: Lista Rezervacija sa potrebnim akcijama agenata - '.Carbon::now()->format('Y-m-d h:i:s'));

    }

    public function build()
    {
        return $this->view('emails.vec.alert');
    }
}
