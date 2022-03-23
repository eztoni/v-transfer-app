<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Company extends Model
{
    use SoftDeletes;
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'name',
        'contact',
        'email',
        'country_id',
        'city',
        'zip',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function languages(){
        return $this->belongsToMany(Language::class);
    }

}
