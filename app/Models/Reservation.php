<?php

namespace App\Models;

use Database\Seeders\TransferExtrasPriceSeeder;
use Illuminate\Database\Eloquent\Model;
use Money\Money;

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
