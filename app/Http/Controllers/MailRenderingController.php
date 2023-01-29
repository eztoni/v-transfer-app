<?php

namespace App\Http\Controllers;

use App\Mail\Guest\GuestReservationConfirmationMail;

class MailRenderingController extends Controller
{
    public function renderReservationMail($type,$id)
    {
        switch ($type){
            case 'CONFIRMATION':
               return  new GuestReservationConfirmationMail($id);
                break;
            case 'MODIFY':
                return  new \App\Mail\Guest\GuestReservationCancellationMail($id);
                break;
            case 'CANCEL':
                break;
        }

    }
}
