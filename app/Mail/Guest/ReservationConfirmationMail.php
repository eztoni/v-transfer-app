<?php

namespace App\Mail\Guest;

use App\Models\Reservation;
use App\Models\Traveller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailable;

class ReservationConfirmationMail extends Mailable
{
    public Reservation $reservation;

    public function __construct($reservation_id, $locale)
    {
        $this->reservation = Reservation::findOrFail($reservation_id);

        \App::setLocale($locale);

        $this->subject(__('mail.guest.confirmation_mail.subject'));

        $pdf = PDF::loadView('attachments.voucher', ['reservation'=>$this->reservation]);
        $this->attachData($pdf->output(),'Voucher.pdf');

        $pdf = PDF::loadView('attachments.booking_confirmation', ['reservation'=>$this->reservation]);
        $this->attachData($pdf->output(),'booking_confirmation.pdf');

    }

    public function build()
    {
        return $this->view('emails.guest.confirmation');
    }
}
