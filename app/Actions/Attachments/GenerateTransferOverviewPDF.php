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


        $reservations = Reservation::query()
            ->whereIsMain(true)
            ->with(['leadTraveller', 'pickupLocation', 'dropoffLocation', 'returnReservation'])
            ->where('status',Reservation::STATUS_CONFIRMED)
            ->where(function ($q) use($from,$to) {
                $q->where(function ($q) use($from,$to){
                    $q->whereDate('date_time', '>=', $from)
                        ->whereDate('date_time', '<=',  $to);
                })->orWHereHas('returnReservation',function ($q)use($from,$to){
                    $q->whereDate('date_time', '>=',  $from)
                        ->whereDate('date_time', '<=',  $to);
                });
            })
            ->get()
            ->map(function (Reservation $i) {
                return [
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
            })->toArray();








        return \Barryvdh\DomPDF\Facade\Pdf::loadView('attachments.reservation-overview', [
            'from'=>$from,
            'to'=>$to,
            'reservations' => $reservations
        ])->setPaper('a4', 'landscape');
    }

}
