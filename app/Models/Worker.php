<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Worker extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'surname',
        'OIB',
        'email',
        'phone_number',
        'city',
        'employment_date'
    ];

    public function pastTasks()
    {
        return $this->belongsToMany(PastTask::class,'past_task_workers');
    }

}
