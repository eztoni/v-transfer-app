<?php

namespace App\Models;

use App\Casts\ModelCast;
use Database\Seeders\TransferExtrasPriceSeeder;
use Illuminate\Database\Eloquent\Model;
use Money\Money;

class Reservation extends Model
{
    protected $casts = ['transfer'=>'array','route'=>'array','date'=>'date'];

    public const  CONFIRMATION_LANGUAGES = [
        'en'=>'English',
        'hr'=>'Hrvatski',
        'de'=>'German',
        'fr'=>'French',
    ];


    public function createNewTwoWayReservation($dateFrom, $timeFrom)
    {

    }



    public function travellers(){
        return $this->belongsToMany( Traveller::class)->withPivot(['lead','comment']);
    }

    public function otherTravellers(){
        return $this->belongsToMany( Traveller::class)->withPivot(['lead','comment'])->where('lead','=',false);
    }

    public function leadTraveller(){
        return $this->belongsToMany( Traveller::class,'reservation_traveller')->withPivot(['lead','comment'])->where('lead','=',true);
    }

    public function getLeadTravellerAttribute()
    {
        return $this->leadTraveller()->first();
    }

    public function pickupLocation(){
        return $this->hasOne(Point::class,'id','pickup_location');
    }

    public function getNumPassangersAttribute(){
        return (Int)$this->adults + (Int)$this->children + (Int)$this->infants;
    }

    public function getPrice(){
        return \Cknow\Money\Money::EUR($this->price);
    }

}
