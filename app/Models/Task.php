<?php

namespace App\Models;

use Carbon\Carbon;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;

    use SoftDeletes, CascadeSoftDeletes;
    protected $cascadeDeletes = ['alarm'];

    protected $fillable = [
        'partner_id',
        'name',
        'start_date',
        'end_date',
        'price',
        'recurring_value',
        'recurring_metrics'
    ];

    public function partners()
    {
        return $this->belongsTo(Partner::class);
    }

    public function alarm()
    {
        return $this->hasOne(Alarm::class);
    }

    public function getNextDateAttribute()
    {
        $dateNow = Carbon::today();
        $taskDay =self::calculateTaskNextOccurrenceAfterGivenDate($this, $dateNow);
        return $taskDay;
    }

    public static function calculateTaskNextOccurrenceAfterGivenDate($task, $date)
    {
        $startDate = Carbon::parse($task->start_date);
        $recurringValue = $task->recurring_value;
        $recurringMetrics = $task->recurring_metrics;
        if ($date->lte($startDate)) {
            $taskDay = $startDate;
        } else {
            if ($recurringMetrics == 'month') {
                $diff = $date->floatDiffInMonths($startDate);
                $diff = ceil($diff);
            } else {
                $diff = $date->diffindays($startDate);
            }
            if ($diff % $recurringValue != 0) {
                $diff += $recurringValue - ($diff % $recurringValue);
            }
            if ($recurringMetrics == 'month') {
                $taskDay = $startDate->addMonths($diff);
            } else {
                $taskDay = $startDate->addDays($diff);
            }
        }
        return $taskDay;
    }

    public static function forDateRange($date1, $date2)
    {
        $date1 = Carbon::parse($date1);
        $date2 = Carbon::parse($date2);
        $output = array();
        $tasks = Task::all();

        if (!empty($tasks)) {
            foreach ($tasks as $task) {
                $taskDay =self::calculateTaskNextOccurrenceAfterGivenDate($task, $date1);
                while ($taskDay->lte($date2) && Carbon::parse($task->end_date)->gte($taskDay)) {
                    $output[$taskDay->format('Y-m-d')][$task->id] = $task;
                    if ($task->recurring_metrics == 'month') {
                        $taskDay->addMonths($task->recurring_value);
                    } else {
                        $taskDay->addDays($task->recurring_value);
                    }
                }
            }
        }
        uksort($output, function ($a, $b) {
            if ($a == $b) return 0;
            return ($a < $b) ? -1 : 1;
        });
        return $output;
    }

    public static function boot()
    {
        static::created(function ($model) {
            Alarm::create( ['task_id' => $model->id,'before_offset' => 3]);
        });
        parent::boot();
    }


}
