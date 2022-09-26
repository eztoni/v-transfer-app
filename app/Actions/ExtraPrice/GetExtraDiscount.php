<?php

namespace App\Actions\ExtraPrice;

use App\Facades\EzMoney;

use App\Models\Extra;
use Illuminate\Support\Collection;

use Lorisleiva\Actions\Concerns\AsAction;
use Money\Currency;
use Money\Money;

class GetExtraDiscount
{
    use AsAction;

    public function handle(Extra $extra,$partner_id,$newDiscount = '',$newPrice = null)
    {


        if (!$pivot_partner =  $extra->partner->where('id', $partner_id)->first()?->pivot){
            return 0;
        }

        $discount = $pivot_partner->discount;
        $amount = $pivot_partner->price;


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
