<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{


    protected $fillable = [
        'name',
        'destination_id',
        'starting_point_id',
        'ending_point_id',
        'his_code',
        'active',
    ];

    public function destination(){
        return $this->belongsTo(Destination::class);
    }

}
