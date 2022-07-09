<?php

namespace App\Models;

use App\Casts\ModelCast;
use App\Scopes\DestinationScope;
use Database\Seeders\TransferExtrasPriceSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Money\Money;

class Reservation extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    const  STATUS_ARRAY = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_CANCELLED,
    ];

    protected $casts = [
        'child_seats' => 'array',
        'price_breakdown' => 'array',
        'date_time' => 'datetime',
    ];

    public const  CONFIRMATION_LANGUAGES = [
        'en' => 'English',
        'hr' => 'Hrvatski',
        'de' => 'German',
        'fr' => 'French',
    ];

    public const TRAVELLER_TITLES = [
        'mr' => 'Mr',
        'mrs' => 'Mrs',
        'ms' => 'Ms',
    ];

    public function isCancelled(){
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isPending(){
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed(){
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isRoundTrip()
    {
        return !empty($this->round_trip_id);
    }

    public function getIsRoundTripAttribute()
    {
        return !empty($this->round_trip_id);
    }

    public function getNumPassangersAttribute()
    {
        return (int)$this->adults + (int)$this->children + (int)$this->infants;
    }

    public function getLeadTravellerAttribute()
    {
        return $this->leadTraveller()->first();
    }

    public function getPrice()
    {
        return \Cknow\Money\Money::EUR($this->price);
    }

    public function leadTraveller()
    {
        return $this->belongsToMany(Traveller::class, 'reservation_traveller')->withPivot(['lead', 'comment'])->where('lead', '=', true);
    }

    public function travellers()
    {
        return $this->belongsToMany(Traveller::class)->withPivot(['lead', 'comment']);
    }

    public function otherTravellers()
    {
        return $this->belongsToMany(Traveller::class)->withPivot(['lead', 'comment'])->where('lead', '=', false);
    }

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function extras()
    {
        return $this->belongsToMany(Extra::class);
    }


    public function pickupLocation()
    {
        return $this->hasOne(Point::class, 'id', 'pickup_location');
    }

    public function dropoffLocation()
    {
        return $this->hasOne(Point::class, 'id', 'dropoff_location');
    }

    public function returnReservation()
    {
        return $this->hasOne(Reservation::class, 'id', 'round_trip_id')->where('is_main', false);
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function updatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }


    protected static function booted()
    {
        static::addGlobalScope(new DestinationScope());
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->isDirty('created_by') && auth()->user()) {
                $model->created_by = auth()->user()->id;
            }
        });

        static::updating(function ($model) {
            if (!$model->isDirty('updated_by') && auth()->user()) {
                $model->updated_by = auth()->user()->id;
            }
        });
    }
}
