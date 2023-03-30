<?php

namespace App\Actions\Mail;


use App\Models\Destination;
use App\Models\Owner;
use App\Models\Point;
use App\Models\Reservation;

class GetMailHeaderAddressAndName
{
    public static function run(Reservation $reservation):string
    {



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



    }

}
