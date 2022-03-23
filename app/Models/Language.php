<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'name',
        'language_code',
    ];

    public function companies(){
        return $this->belongsToMany(Company::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope(true));
    }

}
