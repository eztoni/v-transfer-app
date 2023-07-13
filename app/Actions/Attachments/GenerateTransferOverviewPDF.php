<?php

namespace App\Actions\Attachments;

use App\Models\Reservation;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GenerateTransferOverviewPDF
{

    public static function generate(Carbon $from, Collection $reservations,$property_name):PDF
    {

        $bookings = array();

        foreach($reservations as  $i){
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
                'reservation'=>$i
            ];
        }

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('attachments.reservation-overview', [
            'from'=>$from,
            'hotel' => $property_name,
            'reservations' => $bookings
        ])->setPaper('a4', 'landscape');
    }
}
