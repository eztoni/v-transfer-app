<?php

namespace App\Casts;

use App\Facades\EzMoney;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class EzMoneyCast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        return $value ;
    }

    public function set($model, $key, $value, $attributes)
    {
        return EzMoney::parseForDb($value);
    }
}
