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

        switch ($this->reservation->confirmation_language){
            case 'en':
                $booking_confirmation = 'Your Booking Confirmation';
                break;
            case 'hr':
                $booking_confirmation = 'Potvrda vaše rezervacije';
                break;

            case 'de':
                $booking_confirmation = 'Ihre Buchingsbestätigung';
                break;

            case 'it':
                $booking_confirmation = 'Conferma della Sua prenotazione';
                break;
        }

        $pdf = PDF::loadView('attachments.voucher', ['reservation'=>$this->reservation])->setPaper('A4', 'portrait');
        $this->attachData($pdf->output(),"Voucher_{$reservation_id}.pdf");

        $pdf = PDF::loadView('attachments.booking_confirmation', ['reservation'=>$this->reservation])->setPaper('A4', 'portrait');
        $this->attachData($pdf->output(),"{$booking_confirmation}_{$reservation_id}.pdf");

    }

    public function build()
    {
        return $this->view('emails.guest.confirmation');
    }
}
