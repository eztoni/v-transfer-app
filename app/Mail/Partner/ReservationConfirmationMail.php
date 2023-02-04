<?php

namespace App\Mail\Partner;

use App\Models\Reservation;
use App\Models\Traveller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailable;

class ReservationConfirmationMail extends Mailable
{
    public Reservation $reservation;

    public function __construct($reservation_id)
    {
        $this->reservation = Reservation::findOrFail($reservation_id);

        \App::setLocale('hr');

        $this->subject('Potvrda rezervacije transfera #'.$reservation_id);

        $pdf = PDF::loadView('attachments.voucher', ['reservation'=>$this->reservation]);
        $this->attachData($pdf->output(),'Voucher.pdf');



    }

    public function build()
    {
        return $this->view('emails.partner.confirmation');
    }
}
