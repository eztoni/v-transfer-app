<?php

namespace App\Traits;

use App\Models\User;

trait FieldMask
{

    public function getAttribute($name)
    {
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
        return parent::getAttribute($name);
    }


}
