<?php

namespace App\Mail\Partner;

use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailable;

class ReservationModificationMail extends Mailable
{
    public $reservation;

    public function __construct($reservation_id)
    {
        $this->reservation = Reservation::findOrFail($reservation_id);
        \App::setLocale('hr');

        $this->subject('Potvrda modifikacije transfera #'.$reservation_id);

        $pdf = PDF::loadView('attachments.voucher', ['reservation'=>$this->reservation]);
        $this->attachData($pdf->output(),"Voucher_{$reservation_id}.pdf");


        if(!empty($this->reservation->hasModifications())){
            $this->reservation->setModificationsAsSent();
        }
    }

    public function build()
    {
        return $this->view('emails.partner.modification');
    }
}
