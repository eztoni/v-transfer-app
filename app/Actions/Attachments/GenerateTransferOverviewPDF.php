<?php

namespace App\Actions\Attachments;

use App\Models\Reservation;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Models\Point;

class GenerateTransferOverviewPDF
{

    public static function generate(Carbon $from, Carbon $to, Collection $reservations,$hotel):PDF
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


            $date_time = $i->date_time?->format('Y-m-d');

            $pickup_point = Point::findOrFail($i->pickup_address_id);
            $dropoff_point = Point::findOrFail($i->dropoff_address_id);


            $route = $pickup_point->name.' => '.$dropoff_point->name;

            $return_date = $i->returnReservation?->date_time?->format('Y-m-d');

            if($return_date){
                if($return_date <= $to->format('Y-m-d') && $return_date >= $from->format('Y-m-d')){

                    $date_time = $i->returnReservation?->date_time?->format('d.m.Y @ H:i');

                    $pickup_point = Point::findOrFail($i->returnReservation->pickup_address_id);
                    $dropoff_point = Point::findOrFail($i->returnReservation->dropoff_address_id);

                    $route = $pickup_point->name.' => '.$dropoff_point->name;

                }else{
                    $date_time = $i->date_time?->format('d.m.Y @ H:i');
                }
            }else{
                $date_time = $i->date_time?->format('d.m.Y @ H:i');
            }

            $bookings[] = [
                'id' => $i->id,
                'name' => $i->leadTraveller?->full_name,
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
                'opera_confirmation_id' => $opera_confirmation_id,
                'formatted_route' => $route,
                'formatted_date_time' => $date_time
            ];
        }

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('attachments.reservation-overview', [
            'from'=>$from,
            'to'=>$to,
            'hotel' => $hotel,
            'reservations' => $bookings
        ])->setPaper('a4', 'landscape');
    }
}
