<?php

namespace App\Actions\Mail;


use App\Models\Point;
use App\Models\Reservation;
use App\Models\Destination;

class GetMailHeaderAddressAndName
{
    public static function run(Reservation $reservation):string
    {

        switch ($reservation->destination->owner_id){
            case 1: $header = "Valamar Riviera d.d<br/>

                    Stancija Kaligari 1<br/>
                    Poreč, Republika Hrvatska<br/>
                   <br/>
                   OIB: 36201212847
                   <br/>
                   IBAN: HR4123600001101319202<br/>
                   <i>Privacy policy: https://valamar-riviera.com/en/privacy-policy/</i>
                   ";
                break;
            case 2:
                $header = "Valamar Riviera d.d<br/>
                    Stancija Kaligari 1<br/>
                    Poreč, Republika Hrvatska<br/>
                   <br/>
                   OIB: 36201212847
                   <br/>
                   <i>On behalf of and for the account</i><br/>
                   Imperial Riviera<br/>
                   Jurja Barakovića 2<br/>
                   Rab, Republika Hrvatska<br/>
                   OIB: 90896496260<br/>
                   IBAN: HR8124020061100210497

                   <i>Privacy Policy: https://valamar-riviera.com/en/privacy-policy/</i>
                   ";
        }

        return $header;

    }

}
