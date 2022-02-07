<?php
/**
 * Created by PhpStorm
 * User: Tin Modrić
 * Date: 10/18/2021
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PastTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'partner_id',
        'date_completed',
        'name',
    ];

    public function workers()
    {
        return $this->belongsToMany(Worker::class,'past_task_workers')->withTrashed();;
    }
    public function partner()
    {
        return $this->belongsTo(Partner::class)->withTrashed();
    }
    public function comment()
    {
        return $this->hasOne(Comment::class);
    }

}
