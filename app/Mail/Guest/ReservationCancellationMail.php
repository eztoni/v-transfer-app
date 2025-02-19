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

        $pdf = PDF::loadView('attachments.booking_cancellation', ['reservation'=>$this->reservation])->setPaper('A4', 'portrait');
        $this->attachData($pdf->output(),"{$booking_cancellation}_{$reservation_id}.pdf");

        $pdf = PDF::loadView('attachments.voucher', ['reservation'=>$this->reservation])->setPaper('A4', 'portrait');
        $this->attachData($pdf->output(),"BookingVoucher_{$reservation_id}.pdf");

        $cf_null = 0;

        if($this->reservation->cf_null == 1){
            $cf_null = 1;
        }elseif($this->reservation->isRoundTrip()){
            if($this->reservation->returnReservation->cf_null == 1){
                $cf_null = 1;
            }
        }

        if($this->reservation->hasCancellationFee() && $cf_null == 0){
            $pdf_cf = PDF::loadView('attachments.booking_cancellation_fee',['reservation'=>$this->reservation])->setPaper('A4', 'portrait');
            $this->attachData($pdf_cf->output(),"{$booking_cancellation_fee}_{$reservation_id}.pdf");
        }
    }

    public function build()
    {
        return $this->view('emails.guest.cancellation');
    }
}
