<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\App;

trait FieldMask
{

    public function getAttribute($name)
    {
        if (App::environment('production')) {
            if (!app()->runningInConsole()) {
                if (property_exists($this, 'masked')) {
                    if (is_array($this->masked)) {
                        foreach ($this->masked as $fieldName) {
                            if ($name == $fieldName) {
                                if(is_string(parent::getAttribute($name))){
                                    return \Str::mask(parent::getAttribute($name), '*', 3);
                                }
                            }
                        }
                    }
                }
            }
        }
        return parent::getAttribute($name);
    }


}
