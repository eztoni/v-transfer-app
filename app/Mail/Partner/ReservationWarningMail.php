<?php

namespace App\Mail\Partner;

use App\Models\Reservation;
use App\Models\Traveller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailable;

class ReservationWarningMail extends Mailable
{
    public Reservation $reservation;

    public function __construct($reservation_id)
    {
        $this->reservation = Reservation::findOrFail($reservation_id);

        \App::setLocale('hr');

        $this->subject('Upozorenje za rezervaciju #'.$reservation_id);
    }

    public function build()
    {
        return $this->view('emails.vec.warning');
    }
}
