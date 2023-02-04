<?php

namespace App\Actions\Breakdown;

use App\Facades\EzMoney;
use App\Models\Route;
use App\Models\Transfer;
use Illuminate\Support\Collection;

use Lorisleiva\Actions\Concerns\AsAction;
use Money\Currency;
use Money\Money;

class GetPriceBreakdownItemLabel
{
    use AsAction;

    public function handle(array $breakdownItem)
    {
        switch (\Arr::get($breakdownItem,'item')){
            case 'extra':
                return __('mail.extra'). ' - '.\Arr::get($breakdownItem,'model')?->name;
                break;
            default:
                $label = __('mail.transfer_price');

                $route = \Arr::get($breakdownItem,'price_data.route_id');

                if($route = Route::find($route)){
                    $label = $route->name;
                }



                return $label;
                break;
        }

    }
}
