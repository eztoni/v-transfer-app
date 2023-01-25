<?php

namespace App\Pivots;

use App\Casts\EzMoneyCast;
use App\Models\Extra;
use App\Models\Partner;
use Money\Currency;
use Money\Money;

class ExtraPartner extends \Illuminate\Database\Eloquent\Relations\Pivot
{

    public const TAX_LEVEL_PPOM = 'PPOM';
    public const TAX_LEVEL_RPO = 'RPO';

    public const CALC_TYPE_PI = 'Per Item';
    public const CALC_TYPE_PP = 'Per Person';

    protected $attributes = [
        'discount' => 0,
        'commission' => 0,
        'price' => 0,
    ];

    protected $appends = [
        'price_with_discount',
        'price_with_commission',
    ];

    protected $casts = [
        'price' => EzMoneyCast::class,
        'date_from'=>'datetime:Y-m-d',
        'date_to'=>'datetime:Y-m-d',
    ];


    public function getPriceWithDiscountAttribute()
    {
        $money = new Money($this->price,new Currency('EUR'));
        $money =  $money->allocate([$this->discount,100 - $this->discount]);
        return (int)  $money[1]->getAmount();
    }

    public function getPriceWithCommissionAttribute()
    {
        $money = new Money($this->price,new Currency('EUR'));
        $commissions =  $money->allocate([$this->commission,100 - $this->commission]);
        return  (int) $commissions[0]->getAmount();

    }

    public function extra() {
        return $this->belongsTo(Extra::class);
    }

    public function partner() {
        return $this->belongsTo(Partner::class);
    }
}
