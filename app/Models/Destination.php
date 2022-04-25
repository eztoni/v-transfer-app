<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use App\Scopes\OwnerScope;
use App\Traits\FieldMask;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Destination extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;
    use FieldMask;

    protected $masked = ['name'];

    protected $fillable = [
        'name',
        'owner_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function points(){
        return $this->hasMany(Point::class);
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new OwnerScope());
    }
}
