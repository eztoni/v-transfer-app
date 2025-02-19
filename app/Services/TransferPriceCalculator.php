<?php

namespace App\Services;

use App\Pivots\ExtraPartner;
use App\Pivots\TransferPricePivot;
use Cknow\Money\Money;
use Illuminate\Support\Facades\DB;

class TransferPriceCalculator
{

    private $transferId;
    private $partnerId;
    private $routeId;
    private $extraIds = [];
    private $roundTrip;

    private $breakdownArray;

    private $price;

    private $breakdownLang = 'en';
    private $priceData;
    private $extrasPriceData;

    /**
     * @param $transferId
     * @param $partnerId
     * @param $routeId
     */
    public function __construct($transferId, $partnerId, $roundTrip, $routeId, $extraIds)
    {
        $this->transferId = $transferId;
        $this->partnerId = $partnerId;
        $this->routeId = $routeId;
        $this->roundTrip = $roundTrip;
        $this->extraIds = $extraIds;

        $this->setPriceData();
        $this->setExtrasPriceData();
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

    public function setBreakdownLang($lang)
    {
        $this->breakdownLang = $lang;
        return $this;
    }

    private function calculatePrice()
    {
        if (empty($this->transferId) || empty($this->partnerId) || empty($this->routeId)) {
            return null;
        }


        if (!$this->priceData) {
            return null;
        }

        if($this->roundTrip){

            $roundTripPrice = $this->priceData->price_round_trip/2;

            $price = Money::EUR(
                $roundTripPrice
            );

        }else{
            $price = Money::EUR(
                $this->priceData->price
            );
        }

        $this->breakdownArray[] = [
            'item' => 'transfer_price',
            'amount' => $price,
            'price_data' =>  $this->priceData->toArray()
        ];

        /** @var ExtraPartner $exPrice */
        foreach ($this->extrasPriceData as $exPrice) {

            $extra_quantity = !empty($this->extraIds[$exPrice->extra_id]) ? $this->extraIds[$exPrice->extra_id] : 0;

            $money = Money::EUR(
                $exPrice->price
            );

            if($extra_quantity > 0){

                for($i = 0;$i < $extra_quantity;$i++){

                    $this->breakdownArray[] = [
                        'item' => 'extra',
                        'item_id' => $exPrice->extra->id,
                        'model' => $exPrice->extra->toArray(),
                        'amount' => $money,
                        'price_data' => $exPrice->withoutRelations()->toArray()
                    ];

                    $price = $price->add($money->getMoney());
                }
            }
        }

        $this->price = $price;
    }


    public function getPriceData()
    {
        return $this->priceData;
    }


    public function getExtrasPriceData()
    {
        return $this->extrasPriceData;
    }


    private function setPriceData(): void
    {
        $this->priceData = TransferPricePivot::wherePartnerId( $this->partnerId)
            ->whereRouteId($this->routeId)
            ->whereTransferId($this->transferId)->first();

    }

    private function setExtrasPriceData(): void
    {
        $this->extrasPriceData =  ExtraPartner::where('partner_id', $this->partnerId)
            ->with('extra')
            ->whereIn('extra_id', array_keys($this->extraIds))
            ->get();

    }

    /**
     * @param mixed $transferId
     * @return TransferPriceCalculator
     */
    public function setTransferId($transferId)
    {
        $this->transferId = $transferId;
        return $this;
    }

    /**
     * @param mixed $partnerId
     * @return TransferPriceCalculator
     */
    public function setPartnerId($partnerId)
    {
        $this->partnerId = $partnerId;
        return $this;
    }

    /**
     * @param mixed $routeId
     * @return TransferPriceCalculator
     */
    public function setRouteId($routeId)
    {
        $this->routeId = $routeId;
        return $this;
    }


}
