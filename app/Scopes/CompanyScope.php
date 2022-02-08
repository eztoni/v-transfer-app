<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CompanyScope implements \Illuminate\Database\Eloquent\Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('company_id', '=', Auth::user()->company_id);
    }
}
