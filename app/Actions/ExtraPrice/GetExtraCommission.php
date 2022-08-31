<?php

namespace App\Actions\ExtraPrice;

use App\Facades\EzMoney;
use App\Models\Extra;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use Money\Currency;
use Money\Money;

class GetExtraCommission
{
    use AsAction;

    public function handle(Extra $extra,$partner_id,$newCommission = '',$newPrice = null)
    {


        $pivot_partner =  $extra->partner->where('id', $partner_id)->first()->pivot;
        $commission = $pivot_partner->commission;
        $amount = $pivot_partner->price;


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

    }
}
