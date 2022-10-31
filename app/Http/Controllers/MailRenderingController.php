<?php

namespace App\Http\Controllers;

class MailRenderingController extends Controller
{
    public function renderReservationMail($type,$id)
    {
        switch ($type){
            case 'CONFIRMATION':
               return  new \App\Mail\ConfirmationMail($id);
                break;
            case 'MODIFY':
                return  new \App\Mail\ModificationMail($id);
                break;
        }

    }
}
