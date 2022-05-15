<?php

namespace App\Services\Helpers;

use App\Models\Reservation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReservationPartnerOrderCache
{

    private int $destinationId;



    public function __construct(int $destinationId)
    {
        $this->destinationId = $destinationId;
    }

    public function cacheDestinationPartners(){
        Cache::put('partner_order_'.$this->destinationId,$this->getDestinationPartnerOrderFromDB());
    }


    public function getPartnerOrder(){

        return Cache::rememberForever('partner_order_'.$this->destinationId, function () {
            return $this->getDestinationPartnerOrderFromDB();
        });
    }

    private function getDestinationPartnerOrderFromDB(){
        return DB::table('reservations')->select('partner_id','id')->where('destination_id',$this->destinationId)->orderByDesc('id')->limit(30)->get()
            ->unique('partner_id')
            ->pluck('partner_id')
            ->toArray();
    }

}
