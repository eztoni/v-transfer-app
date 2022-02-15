<?php

namespace App\Models;

use App\Scopes\CompanyScope;
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


    protected $fillable = [
        'company_id',
        'name',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }


    public function company(){

        return $this->belongsTo(Company::class);
    }


    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope());
    }
}
