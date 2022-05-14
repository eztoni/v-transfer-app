<?php

namespace App\Casts;

use App\Models\Transfer;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class ModelCast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        $toArray = json_decode($value,true);

        return (new $toArray['_modelClassName']())->fill($toArray);
    }

    public function set($model, $key, $value, $attributes)
    {
        if (! $value instanceof Model) {
            throw new \InvalidArgumentException('The given value is not an Address instance.');
        }
        $value->_modelClassName = get_class($value);

        return $value->toJson();
    }
}
