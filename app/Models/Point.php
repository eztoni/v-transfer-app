<?php

namespace App\Models;

use App\Scopes\ActiveScope;
use App\Scopes\CompanyScope;
use App\Scopes\DestinationScope;
use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Translatable\HasTranslations;

class Point extends Model
{
    use HasFactory;
    use LogsActivity;
    use HasTranslations;

    public array $translatable = ['name'];

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
        'reception_email',
        'longitude',
        'type',
        'fiskal_id',
        'pms_code',
        'pms_class',
        'active',
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function owner(){
        return $this->belongsTo(Owner::class);
    }
    public function destination(){
        return $this->belongsTo(Destination::class);
    }

    public function getInternalNameAttribute($vale)
    {
        return $vale?: $this->name;
    }

    public function scopeCityOnly($q){
        return  $q->where('type',Point::TYPE_CITY);
    }
    public function scopeNotCity($q){
        return  $q->where('type','!=',Point::TYPE_CITY);
    }

    protected static function booted()
    {
        static::addGlobalScope(new ActiveScope());
        static::addGlobalScope(new DestinationScope());
    }


}
