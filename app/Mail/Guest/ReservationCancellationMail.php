<?php

namespace App\Mail\Guest;

use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailable;

class ReservationCancellationMail extends Mailable
{

    public $reservation;


    public function __construct($reservation_id, $locale)
    {
        $this->reservation = Reservation::findOrFail($reservation_id);

        \App::setLocale($locale);

        $booking_cancellation = 'Booking Cancellation';
        $booking_cancellation_fee = 'CancellationFee';

        if($locale == 'hr'){
            $booking_cancellation = 'Otkaz Rezervacije';
            $booking_cancellation_fee = 'NaknadaŠtete';
        }

        $this->subject(__('mail.guest.cancellation_mail.subject'));

        $pdf = PDF::loadView('attachments.booking_cancellation', ['reservation'=>$this->reservation]);
        $this->attachData($pdf->output(),"{$booking_cancellation}_{$reservation_id}.pdf");

        if($this->reservation->hasCancellationFee()){
            $pdf_cf = PDF::loadView('attachments.booking_cancellation_fee',['reservation'=>$this->reservation]);
            $this->attachData($pdf->output(),"{$booking_cancellation_fee}_{$reservation_id}.pdf");
        }
    }

    public function build()
    {
        return $this->view('emails.guest.cancellation');
    }
}
