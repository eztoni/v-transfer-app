<?php

namespace App\Services;

use App\Pivots\ExtraPartner;
use Cknow\Money\Money;
use Illuminate\Support\Facades\DB;

class TransferPrices
{

    private $transferId;
    private $partnerId;
    private $routeId;
    private $extraIds = [];

    /**
     * @param $transferId
     * @param $partnerId
     * @param $routeId
     */
    public function __construct($transferId, $partnerId, $routeId )
    {
        $this->transferId = $transferId;
        $this->partnerId = $partnerId;
        $this->routeId = $routeId;
    }


    public function getPrice():mixed
    {
        if(empty($this->transferId) || empty($this->partnerId) || empty($this->routeId)){
            return null;
        }

        $price =  $this->getRouteData();

        $extrasPrices = ExtraPartner::where('partner_id',$this->partnerId)->whereIn('extra_id',$this->extraIds)->get();

        if(!$price){
            return null;
        }
        $price = Money::EUR(
            $price->price
        );
        foreach ($extrasPrices as $exPrice){
            $price =  $price->add(Money::EUR(
                $exPrice->price
            )->getMoney());
        }

        return $price;
    }

    public function getRouteData(){
        return DB::table('route_transfer')
            ->where('route_id',$this->routeId)
            ->where('partner_id',$this->partnerId)
            ->where('transfer_id',$this->transferId)
            ->first();
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

    /**
     * @param array $extraIds
     * @return TransferPrices
     */
    public function setExtraIds(array $extraIds = []): TransferPrices
    {
        $this->extraIds = $extraIds;
        return $this;
    }


}
