<?php

namespace App\Actions\Mail;


use App\Models\Point;
use App\Models\Reservation;
use App\Models\Destination;

class GetMailHeaderAddressAndName
{
    public static function run(Reservation $reservation):string
    {

        /*
        if (($reservation->pickupLocation->type === Point::TYPE_ACCOMMODATION && $reservation->dropoffLocation->type === Point::TYPE_ACCOMMODATION)   ){
            return  "Valamar Riviera d.d.
                    <br>
                    52440 Poreč, Croatia";
        }


        if ($reservation->pickupLocation->type === Point::TYPE_ACCOMMODATION){
            return "{$reservation->pickupLocation->name}
                    <br>
                    {$reservation->pickupLocation->address}";

        }elseif($reservation->dropoffLocation->type === Point::TYPE_ACCOMMODATION){
            return "{$reservation->dropoffLocation->name}
                    <br>
                    {$reservation->dropoffLocation->address}";
        }

        return  "Valamar Riviera d.d.
                    <br>
                   Stancija Kaligari 1 52440 Poreč, Croatia";
*/
        $destination = Destination::findOrFail($reservation->destination_id);

        switch ($destination->owner_id){
            case 1:
                return  "Valamar Riviera d.d.
                    <br>
                   Stancija Kaligari 1 52440 Poreč, Croatia";
                break;
            case 2:
                return "Imperial Riviera d.d.
                    <br>
                    Ul. Jurja Barakovića 2, 51280, Rab, Croatia";
        }

        return  "Valamar Riviera d.d.
                    <br>
                   Stancija Kaligari 1 52440 Poreč, Croatia";

    }

}
