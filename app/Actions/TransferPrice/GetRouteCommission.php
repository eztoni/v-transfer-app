<?php

namespace App\Actions\TransferPrice;

use App\Facades\EzMoney;
use App\Models\Transfer;
use Illuminate\Support\Collection;

use Lorisleiva\Actions\Concerns\AsAction;
use Money\Currency;
use Money\Money;

class GetRouteCommission
{
    use AsAction;

    /*public function handle(Transfer $transfer,$roundTrip,int $routeId,$newCommission = '',$newPrice = null)
    {
        $route = $transfer->routes->firstWhere('id', '=', $routeId);
        $commission = $route->pivot->commission;
        $amount = $route->pivot->price;

        if($roundTrip){
            $amount = $route->pivot->price_round_trip;
        }


        if($newCommission >= 0){
            $commission = $newCommission;
        }

        if($newPrice){
            $amount = EzMoney::parseForDb($newPrice);
        }

        $money = new Money($amount,new Currency('EUR'));
        if ($commission <= 0 || $money->isZero()) {
            return 0;
        }

        $list = list($my_cut, $investors_cut) = $money->allocate([$commission, 100 - $commission]);

        return $list[0]->getAmount();

    }*/

    public function handle(Transfer $transfer,$roundTrip,int $routeId,$commission,$price = "0",$roundTripPrice = "0")
    {


        if($price == null){
            $price = "0";
        }

        if($roundTripPrice == null){
            $roundTripPrice = "0";
        }

        $amount = EzMoney::parseForDb($price);

        if($roundTrip){
            $amount = EzMoney::parseForDb($roundTripPrice);
        }

        $money = new Money($amount,new Currency('EUR'));
        if ($commission <= 0 || $money->isZero()) {
            return 0;
        }

        $list = list($my_cut, $investors_cut) = $money->allocate([$commission, 100 - $commission]);

        return $list[0]->getAmount();

    }
}
