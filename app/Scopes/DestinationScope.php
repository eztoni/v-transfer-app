<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DestinationScope implements \Illuminate\Database\Eloquent\Scope
{

    public $isPivot = false;

    public function __construct($isPivot = false)
    {
        $this->isPivot = $isPivot;
    }

    public function apply(Builder $builder, Model $model)
    {
        if (!empty(Auth::user()->id)) {

            if ($this->isPivot) {
                $builder->whereHas('destinations', function (Builder $query) {
                    $query->where('destination_id', '=', \Auth::user()->destination_id);
                });
            } else {
                $builder->where('destination_id', '=', Auth::user()->destination_id);
            }
        }
    }
}
