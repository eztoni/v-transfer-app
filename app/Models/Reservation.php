<?php

namespace App\Models;

use Database\Seeders\TransferExtrasPriceSeeder;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{

    public function createNewTwoWayReservation($dateFrom, $timeFrom)
    {

    }
    public function createNewReservation()
    {

    }

    public function travellers(){
        return $this->belongsToMany( Traveller::class)->withPivot(['lead','comment']);
    }

}
