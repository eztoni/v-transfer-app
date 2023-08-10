<?php

namespace App\Actions\Mail;


use App\Models\Point;
use App\Models\Reservation;
use App\Models\Destination;

class GetMailHeaderAddressAndName
{
    public static function run(Reservation $reservation):string
    {

        $header = '';

            if (($reservation->pickupAddress->type === Point::TYPE_ACCOMMODATION && $reservation->dropoffAddress->type === Point::TYPE_ACCOMMODATION)   ){
                $header =   "Valamar Riviera d.d.
                        <br>
                        52440 Poreč, Croatia";
            }

            if ($reservation->pickupAddress->type === Point::TYPE_ACCOMMODATION){
                $header =  "{$reservation->pickupAddress->name}
                        <br>
                        {$reservation->pickupAddress->address}";

            }elseif($reservation->dropoffAddress->type === Point::TYPE_ACCOMMODATION){
                $header = "{$reservation->dropoffAddress->name}
                        <br>
                        {$reservation->dropoffAddress->address}";
            }


            $header .= '<br/>Telephone: +385 (20) 52 465 000<br/>';
            $header .= 'Fax: +385 (20) 52 451 206';

        switch ($reservation->destination->owner_id){
            case 1: "<br/>HR-AB-52-040020883
                   <br/>
                   OIB: 36201212847
                   <br/>
                   IBAN: HR412360000-1101319202
                   ";
                break;
            case 2:
                $header .= "<br/>
                    OIB: 90896496260
                    <br/>
                    IBAN: HR8124020061100210497
                    ";
        }

        return $header;

    }

}
