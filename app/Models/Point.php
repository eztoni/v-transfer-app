<?php

namespace App\Models;

use App\Scopes\ActiveScope;
use App\Scopes\CompanyScope;
use App\Scopes\DestinationScope;
use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;

    const TYPE_AIRPORT = 'airport';
    const TYPE_ACCOMMODATION = 'accommodation';
    const TYPE_HARBOR = 'harbor';
    const TYPE_CITY = 'city';

    const  TYPE_ARRAY = [
        self::TYPE_AIRPORT,
        self::TYPE_ACCOMMODATION,
        self::TYPE_HARBOR,
        self::TYPE_CITY,
    ];

    protected $fillable = [
        'name',
        'description',
        'address',
        'latitude',
        'longitude',
        'type',
        'pms_code',
        'active',
    ];

    public function owner(){
        return $this->belongsTo(Owner::class);
    }
    public function destination(){
        return $this->belongsTo(Destination::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new ActiveScope());
        static::addGlobalScope(new DestinationScope());
    }
}
