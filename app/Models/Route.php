<?php

namespace App\Models;

use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'his_code',
        'active',
    ];

    protected $with = [
        'startingPoint','endingPoint'
    ];

    public function destination(){
        return $this->belongsTo(Destination::class);
    }

    public function startingPoint()
    {
        return $this->belongsTo(Point::class,'starting_point_id');
    }
    public function endingPoint()
    {
        return $this->belongsTo(Point::class,'ending_point_id');
    }
    protected static function booted()
    {
        static::addGlobalScope(new OwnerScope());
    }
}
