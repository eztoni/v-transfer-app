<?php

namespace App\Services;

use Cknow\Money\Money;
use Illuminate\Support\Facades\DB;

class TransferPrices
{

    private $transferId;
    private $partnerId;
    private $routeId;


    public function getPrice():mixed
    {
        if(empty($this->transferId) || empty($this->partnerId) || empty($this->routeId)){
            return null;
        }

        $price =  DB::table('route_transfer')
            ->where('route_id',$this->routeId)
            ->where('partner_id',$this->partnerId)
            ->where('transfer_id',$this->transferId)
            ->first();

        if(!$price){

            return null;
        }


        return Money::EUR(
            $price->price
        );

    }


    /**
     * @param mixed $transferId
     * @return TransferPrices
     */
    public function setTransferId($transferId)
    {
        $this->transferId = $transferId;
        return $this;
    }

    /**
     * @param mixed $partnerId
     * @return TransferPrices
     */
    public function setPartnerId($partnerId)
    {
        $this->partnerId = $partnerId;
        return $this;
    }

    /**
     * @param mixed $routeId
     * @return TransferPrices
     */
    public function setRouteId($routeId)
    {
        $this->routeId = $routeId;
        return $this;
    }




}
