<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Cknow\Money\Casts\MoneyDecimalCast;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Extra extends Model implements HasMedia
{

    use InteractsWithMedia;
    const MAX_IMAGES = 5;

    protected $fillable = [
        'name',
        'description',
        'price',
    ];

    protected $casts = [
      //cast money as decimal using the currency defined in the package config
      'price' => MoneyDecimalCast::class,
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope());
    }
}
