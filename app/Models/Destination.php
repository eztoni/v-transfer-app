<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Destination extends Model
{
    protected $fillable = [
        'company_id',
        'name',
    ];
    public function company(){
        return $this->belongsTo(Company::class);
    }


    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope());
    }
}
