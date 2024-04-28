<?php

namespace App\Mail\Guest;

use App\Models\Reservation;
use App\Models\Traveller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailable;

class ReservationReceptionReportMail extends Mailable
{
    public $accommodation_name;

    public function __construct($reservation_list,$subject,$date_from,$date_to,$accommodation_name)
    {

        $this->accommodation_name = $accommodation_name;

        $this->subject($subject);

        $pdf = \App\Actions\Attachments\GenerateTransferOverviewPDF::generate(\Carbon\Carbon::make($date_from),\Carbon\Carbon::make($date_to),$reservation_list,$accommodation_name);

        $this->attachData($pdf->output(),"popis_rezervacija_".$date_from.".pdf");

    }

    public function build()
    {
        return $this->view('emails.vec.reception');
    }
}
