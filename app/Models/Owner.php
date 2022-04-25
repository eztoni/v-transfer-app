<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $fillable = [
        'name',
        'company_id'
    ];

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function destinations()
    {
        return $this->hasMany(Destination::class);
    }
}
