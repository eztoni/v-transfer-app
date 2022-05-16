<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Mail\Mailable;

class ConfirmationMail extends Mailable
{
    public $reservation;
    public $lead_traveller;

    public function __construct($reservation_id)
    {
        $this->reservation = Reservation::findOrFail($reservation_id);

        if($this->reservation){
            $this->lead_traveller = $this->reservation->leadTraveller();
        }
    }

    public function build()
    {
        return $this->view('emails.confirmation');
    }
}
