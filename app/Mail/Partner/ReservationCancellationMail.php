<?php

namespace App\Mail\Partner;

use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailable;

class ReservationCancellationMail extends Mailable
{

    public $reservation;


    public function __construct($reservation_id)
    {
        $this->reservation = Reservation::findOrFail($reservation_id);

        \App::setLocale('hr');

        $this->subject('Otkaz rezervacije transfera #'.$reservation_id);

        $pdf = PDF::loadView('attachments.voucher', ['reservation'=>$this->reservation]);
        $this->attachData($pdf->output(),"Voucher_{$reservation_id}.pdf");

        $pdf = PDF::loadView('attachments.booking_confirmation', ['reservation'=>$this->reservation]);
        $this->attachData($pdf->output(),"OtkazRezervacije_{$reservation_id}.pdf");

    }

    public function build()
    {
        return $this->view('emails.partner.cancellation');
    }
}
