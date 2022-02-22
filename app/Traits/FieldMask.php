<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\App;

trait FieldMask
{

    public function getAttribute($name)
    {
        if (App::environment('production')) {
            if (\Auth::user()->hasRole(User::ROLE_SUPER_ADMIN)) {
                if (property_exists($this, 'masked')) {
                    if (is_array($this->masked)) {
                        foreach ($this->masked as $fieldName) {
                            if ($name == $fieldName) {
                                return \Str::mask(parent::getAttribute($name), '*', 3);
                            }
                        }
                    }
                }
            }
        }
        return parent::getAttribute($name);
    }


}
