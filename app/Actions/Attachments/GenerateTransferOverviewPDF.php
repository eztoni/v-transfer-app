<?php

namespace App\Actions\Attachments;

use App\Models\Reservation;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GenerateTransferOverviewPDF
{

    public static function generate(Carbon $from, Carbon $to, Collection $reservations):PDF
    {

        $bookings = array();

        foreach($reservations as  $i){

            $opera_resv_id = '';
            $opera_confirmation_id = '';

            if($i->leadTraveller){
                #Reservation Opera ID
                if($i->leadTraveller->reservation_opera_id){
                    $opera_resv_id = $i->leadTraveller->reservation_opera_id;
                }
                #Reservation Opera Confirmation ID
                if($i->leadTraveller->reservation_opera_confirmation){
                    $opera_confirmation_id = $i->leadTraveller->reservation_opera_confirmation;
                }
            }

            $bookings[] = [
                'id' => $i->id,
                'name' => $i->leadTraveller?->first()->full_name,
                'date_time' => $i->date_time?->format('d.m.Y @ H:i'),
                'partner' => $i->partner->name,
                'adults' => $i->adults,
                'children' => $i->children,
                'infants' => $i->infants,
                'round_trip' => $i->is_round_trip,
                'round_trip_date' => $i->returnReservation?->date_time?->format('d.m.Y @ H:i'),
                'reservation'=>$i,
                'price' => $i->getPrice()->formatByDecimal(),
                'opera_resv_id' => $opera_resv_id,
                'opera_confirmation_id' => $opera_confirmation_id
            ];
        }

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('attachments.reservation-overview', [
            'from'=>$from,
            'to'=>$to,
            'reservations' => $bookings
        ])->setPaper('a4', 'landscape');
    }
}
