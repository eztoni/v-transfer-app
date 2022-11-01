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
    private $roundTrip;

    private $breakdownArray;

    private $price;

    private $breakdownLang = 'en';

    /**
     * @param $transferId
     * @param $partnerId
     * @param $routeId
     */
    public function __construct($transferId, $partnerId, $roundTrip, $routeId,$extraIds)
    {
        $this->transferId = $transferId;
        $this->partnerId = $partnerId;
        $this->routeId = $routeId;
        $this->roundTrip = $roundTrip;
        $this->extraIds = $extraIds;
        $this->calculatePrice();
    }


    public function getPrice(): mixed
    {
        return $this->price;
    }

    public function getPriceBreakdown()
    {
        return $this->breakdownArray;
    }

    public function setBreakdownLang($lang){
        $this->breakdownLang = $lang;
        return $this;
    }

    private function calculatePrice()
    {
        if (empty($this->transferId) || empty($this->partnerId) || empty($this->routeId)) {
            return null;
        }

        $routeData = $this->getRouteData();


        if (!$routeData) {
            return null;
        }

        $price = Money::EUR(
            $this->roundTrip ? $routeData->price_round_trip : $routeData->price
        );
        $this->breakdownArray[]= [
            'item'=>'transfer_price',
            'amount'=>$price
        ];

        $extrasPrices = ExtraPartner::where('partner_id', $this->partnerId)
            ->with('extra')
            ->whereIn('extra_id', $this->extraIds)
            ->get();
        //todo: reservation state?
        foreach ($extrasPrices as $exPrice) {
            $money = Money::EUR(
                $exPrice->price
            );
            $this->breakdownArray[]= [
                'item'=>'extra',
                'item_id' =>$exPrice->extra->id,
                'label'=>$exPrice->extra->getTranslation('name',$this->breakdownLang),
                'amount'=>$money
            ];

            $price = $price->add($money->getMoney());
        }

        $this->price = $price;
    }

    public function getRouteData()
    {
        return DB::table('route_transfer')
            ->where('route_id', $this->routeId)
            ->where('partner_id', $this->partnerId)
            ->where('transfer_id', $this->transferId)
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




}
