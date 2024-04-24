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

        $display_id = $reservation_id;

        if($this->reservation->is_main != 1){
            $main_booking = Reservation::where('round_trip_id',$reservation_id)->get()->first();
            if(!empty($main_booking->status) && $main_booking->status == 'confirmed'){
                $display_id = $main_booking->id;
            }
        }

        $this->subject('Potvrda modifikacije transfera #'.$display_id);

        $pdf = PDF::loadView('attachments.voucher', ['reservation'=>$this->reservation]);
        $this->attachData($pdf->output(),"Voucher_{$display_id}.pdf");

        $pdf = PDF::loadView('attachments.booking_confirmation', ['reservation'=>$this->reservation]);
        $this->attachData($pdf->output(),"Potvrda rezervacije_{$display_id}.pdf");
    }

    public function build()
    {
        return $this->view('emails.partner.modification');
    }
}
