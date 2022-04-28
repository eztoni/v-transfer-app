<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Traveller extends Model
{


    public function reservations(){
        return $this->belongsToMany( Reservation::class)->withPivot(['lead','comment']);
    }


}
