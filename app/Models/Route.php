<?php

namespace App\Models;

use App\Scopes\DestinationScope;
use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Translatable\HasTranslations;

class Route extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['name'];

    protected $fillable = [
        'name',
        'his_code',
        'active',
    ];

    protected $with = [
        'startingPoint','endingPoint'
    ];

    public function transfers(){
        return $this->belongsToMany(Transfer::class)->withPivot(['price','price_round_trip','partner_id','round_trip']);
    }

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
        static::addGlobalScope(new DestinationScope());
    }
}
