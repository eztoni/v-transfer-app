<?php

namespace App\Http\Controllers;

use App\Mail\Guest\ReservationConfirmationMail;
use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;

class MailRenderingController extends Controller
{
    public function renderReservationMail($type, $id)
    {
        $res = Reservation::find($id);

        switch ($type) {
            case 'CONFIRMATION':
                return new ReservationConfirmationMail($id,$res->confirmation_language);
                break;
            case 'MODIFY':
                return new \App\Mail\Guest\ReservationCancellationMail($id,$res->confirmation_language);
                break;
            case 'CANCEL':
                return new \App\Mail\Guest\ReservationCancellationMail($id,$res->confirmation_language);
                break;
            case 'ATTACHMENT_VOUCHER':
                return PDF::loadView('attachments.voucher', ['reservation'=>Reservation::find($id)])->outputHtml();
                break;
            case 'ATTACHMENT_CONFIRMATION':
                return  PDF::loadView('attachments.booking_confirmation', ['reservation'=>Reservation::find($id)])->outputHtml();
                break;
            case 'ATTACHMENT_CANCELLATION':
                return  PDF::loadView('attachments.booking_cancellation', ['reservation'=>Reservation::find($id)])->outputHtml();
                break;
            case 'ATTACHMENT_CANCELLATION_FEE':
                return PDF::loadView('attachments.booking_cancellation_fee', ['reservation'=>Reservation::find($id)])->outputHtml();
                break;
        }

    }


}
