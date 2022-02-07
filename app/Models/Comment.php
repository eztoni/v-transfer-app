<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'task_id',
        'past_task_id',
        'comment'
    ];

    public function partnerComments(){
        return $this->belongsTo(Partner::class);
    }
}
