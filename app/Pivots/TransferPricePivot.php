<?php

namespace App\Pivots;

use App\Casts\EzMoneyCast;
use App\Models\Extra;
use App\Models\Partner;
use App\Models\Route;
use App\Models\Transfer;
use Money\Currency;
use Money\Money;

class TransferPricePivot extends \Illuminate\Database\Eloquent\Relations\Pivot
{

    public const TAX_LEVEL_PPOM = 'PPOM';
    public const TAX_LEVEL_RPO = 'RPO';

    public const CALC_TYPE_PI = 'Per Item';
    public const CALC_TYPE_PP = 'Per Person';

    protected $table = 'route_transfer';

    protected $guarded =[];

    protected $attributes = [
        'discount' => 0,
        'commission' => 0,
        'price' => 0,
        'price_round_trip' => 0,
        'round_trip' => true,

    ];

    protected $appends = [
        'price_with_discount',
        'round_trip_price_with_discount',
        'price_with_commission',
        'round_trip_price_with_commission',
    ];


    protected $casts = [
        'price' => EzMoneyCast::class,
        'price_round_trip' => EzMoneyCast::class,
        'date_from'=>'datetime:Y-m-d',
        'date_to'=>'datetime:Y-m-d',
        'round_trip'=>'boolean',
    ];


    public function getPriceWithDiscountAttribute()
    {
        $money = new Money($this->price,new Currency('EUR'));
        $money =  $money->allocate([$this->discount,100 - $this->discount]);
        return (int)  $money[1]->getAmount();
    }
    public function getRoundTripPriceWithDiscountAttribute()
    {
        $money = new Money($this->price_round_trip,new Currency('EUR'));
        $money =  $money->allocate([$this->discount,100 - $this->discount]);
        return (int) $money[1]->getAmount();
    }

    public function getPriceWithCommissionAttribute()
    {
        $money = new Money($this->price,new Currency('EUR'));
        $commissions =  $money->allocate([$this->commission,100 - $this->commission]);
        return (int) $money->getAmount() + (int) $commissions[0]->getAmount();

    }
    public function getRoundTripPriceWithCommissionAttribute()
    {
        $money = new Money($this->price_round_trip,new Currency('EUR'));
        $commissions =  $money->allocate([$this->commission,100 - $this->commission]);
        return (int) $money->getAmount() + (int) $commissions[0]->getAmount();
    }





    public function partner() {
        return $this->belongsTo(Partner::class);
    }

    public function transfer() {
        return $this->belongsTo(Transfer::class);
    }

    public function route() {
        return $this->belongsTo(Route::class);
    }
}

