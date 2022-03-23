<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CompanyScope implements \Illuminate\Database\Eloquent\Scope
{
    public $isPivot = false;
    public function __construct($isPivot= false)
    {
        $this->isPivot = $isPivot;
    }
    public function apply(Builder $builder, Model $model)
    {
        if(!empty(Auth::user()->id)){
            if($this->isPivot){
                $builder->whereHas('companies', function (Builder $query) {
                    $query->where('company_id', '=', \Auth::user()->company_id);
                });
            }else{
                $builder->where('company_id', '=', Auth::user()->company_id);

            }
        }
}}
