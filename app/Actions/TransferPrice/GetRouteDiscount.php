<?php

namespace App\Actions\TransferPrice;

use App\Facades\EzMoney;
use App\Models\Transfer;
use Illuminate\Support\Collection;

use Lorisleiva\Actions\Concerns\AsAction;
use Money\Currency;
use Money\Money;

class GetRouteDiscount
{
    use AsAction;

    public function handle(Transfer $transfer,$roundTrip,int $routeId,$newDiscount = '',$newPrice = null)
    {
        $route = $transfer->routes->firstWhere('id', '=', $routeId);
        $discount = $route->pivot->discount;
        $amount = $route->pivot->price;

        if($roundTrip){
            $amount = $route->pivot->price_round_trip;
        }


        if($newDiscount >= 0){
            $discount = $newDiscount;
        }

        if($newPrice){
            $amount = EzMoney::parseForDb($newPrice);
        }

        $money = new Money($amount,new Currency('EUR'));
        if ($discount <= 0 || $money->isZero()) {
            return 0;
        }

        $list = list($my_cut, $investors_cut) = $money->allocate([$discount, 100 - $discount]);

        return $amount - $list[0]->getAmount();

    }
}
