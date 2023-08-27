<?php

namespace App\Mail\Guest;

use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailable;

class ReservationModificationMail extends Mailable
{

    public $reservation;


    public function __construct($reservation_id, $locale)
    {
        $this->reservation = Reservation::findOrFail($reservation_id);
        \App::setLocale($locale);

        $this->subject(__('mail.guest.modification_mail.subject'));

        $pdf = PDF::loadView('attachments.voucher', ['reservation'=>$this->reservation]);
        $this->attachData($pdf->output(),"Voucher_{$reservation_id}.pdf");

        $pdf = PDF::loadView('attachments.booking_confirmation', ['reservation'=>$this->reservation]);
        $this->attachData($pdf->output(),"Booking Confirmation_{$reservation_id}.pdf");

        if($this->reservation->hasCancellationFee()){

            if($locale == 'hr'){
                $booking_cancellation_fee = 'Naknada Štete';
            }

            $pdf_cf = PDF::loadView('attachments.booking_cancellation_fee',['reservation'=>$this->reservation]);
            $this->attachData($pdf->output(),"{$booking_cancellation_fee}_{$reservation_id}.pdf");
        }
        
    }

    public function build()
    {
        return $this->view('emails.guest.modification');
    }
}
