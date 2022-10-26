<?php

namespace App\Models;

use App\Scopes\DestinationScope;
use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Partner extends Model
{
    use HasFactory;
    use HasTranslations;
    protected $fillable = [
        'terms'
    ];
    public $translatable = ['terms'];

    public function extras(){
        return $this->belongsToMany(Extra::class);
    }

    public function destinations(){
        return $this->belongsToMany(Destination::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new OwnerScope());
        static::addGlobalScope(new DestinationScope(true));
    }
}
