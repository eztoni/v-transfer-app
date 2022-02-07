<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use HasFactory;
    use SoftDeletes, CascadeSoftDeletes;
    protected $cascadeDeletes = ['tasks'];

    protected $fillable = [
        'business_name',
        'contact_name',
        'contact_number',
        'email',
        'OIB',
        'beginning_of_contract',
        'end_of_contract'
    ];

    public function tasks(){
        return $this->hasMany(Task::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }
}
