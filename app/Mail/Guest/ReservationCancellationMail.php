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

        $booking_cancellation = 'Your Reservation Cancellation';
        $booking_cancellation_fee = 'CancellationFee';

        switch ($this->reservation->confirmation_language){
            case 'hr':
                $booking_cancellation_fee = 'Naknada Štete';
                $booking_cancellation = 'Otkaz vaše Rezervacije';
                break;
            case 'de':
                $booking_cancellation_fee = 'Stornogebühr';
                $booking_cancellation = 'Ihre Bestätigung der Stornierung';
                break;
            case 'it':
                $booking_cancellation_fee = 'Tassa di cancellazione';
                $booking_cancellation = 'La Sua cancellazione della prenotazione';
                break;
        }

        $this->subject(__('mail.guest.cancellation_mail.subject'));

        $pdf = PDF::loadView('attachments.booking_cancellation', ['reservation'=>$this->reservation]);
        $this->attachData($pdf->output(),"{$booking_cancellation}_{$reservation_id}.pdf");

        if($this->reservation->hasCancellationFee()){
            $pdf_cf = PDF::loadView('attachments.booking_cancellation_fee',['reservation'=>$this->reservation]);
            $this->attachData($pdf_cf->output(),"{$booking_cancellation_fee}_{$reservation_id}.pdf");
        }
    }

    public function build()
    {
        return $this->view('emails.guest.cancellation');
    }
}
