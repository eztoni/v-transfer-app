<?php

namespace App\Models;

use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [

    ];

    public function destination(){
        return $this->belongsTo(Destination::class);
    }

    public function extras(){
        return $this->belongsToMany(Extra::class);
    }

    public function destinations(){
        return $this->belongsToMany(Destination::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new OwnerScope());
    }
}
