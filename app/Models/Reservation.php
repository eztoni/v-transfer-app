<?php

namespace App\Models;

use App\Casts\ModelCast;
use App\Scopes\DestinationScope;
use App\Services\Api\ValamarOperaApi;
use Database\Seeders\TransferExtrasPriceSeeder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Cknow\Money\Money;
use Illuminate\Validation\Rules\In;

class Reservation extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    const  STATUS_ARRAY = [
        self::STATUS_CONFIRMED,
        self::STATUS_PENDING,
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
        'it' => 'Italian',
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

    public function isSyncedWithOpera(){
        return (bool)$this->opera_sync;
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

    public function getPrice(): Money
    {
        return Money::EUR($this->price);
    }

    public function getPriceHRK(): Money
    {
        $price = $this->price*7.53450;
        return Money::HRK($price);
    }

    public function getVatAmount()
    {
        return Money::EUR($this->price)->multiply($this->included_in_accommodation_reservation ?'0':'0.25');
    }
    public function getPriceWithoutVat()
    {
        return Money::EUR($this->price)->multiply($this->included_in_accommodation_reservation ?'1':'0.75');
    }

    public function leadTraveller()
    {
        return $this->belongsToMany(Traveller::class, 'reservation_traveller')->withPivot(['lead', 'comment'])->where('lead', '=', true);
    }

    public function invoices(){
        return $this->hasMany(Invoice::class);
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
        return $this->belongsTo(Point::class, 'pickup_location','id');
    }
    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function dropoffLocation()
    {
        return $this->belongsTo(Point::class, 'dropoff_location','id' );
    }

    public function pickupAddress()
    {
        return $this->belongsTo(Point::class, 'pickup_address_id','id');
    }

    public function dropoffAddress()
    {
        return $this->belongsTo(Point::class, 'dropoff_address_id','id' );
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

    protected function getTransferPriceCommissionAttribute(): Money
    {

        $total = (int) \Arr::get($this->transfer_price_state,'amount.amount');
        $totalWithCommission = (int) \Arr::get($this->transfer_price_state,$this->is_round_trip?'price_data.round_trip_price_with_commission':  'price_data.price_with_commission');

        $commission =Money::EUR(0);

        if($total && $totalWithCommission){
            $commission=Money::EUR($totalWithCommission );
        }

        return $commission;
    }

    public function getTotalCommissionAmountAttribute():Money
    {
        return $this->transfer_price_commission->add($this->extras_summed_commission);
    }

    public function getOperaLog(){

            $log = ValamarOperaApi::getSyncOperaLog($this->id);

            return $log;
    }

    public function get_extras_list(){

        $extras = array();

        if($this->extras){
            foreach($this->extras as $extra){
                $extras[] = (string)$extra->name;
            }
        }

        return implode(',',$extras);
    }

    protected function getExtrasSummedCommissionAttribute(): Money
    {
        $commission =Money::EUR(0);

        $extraPriceData = $this->extras_price_states;

        foreach ($extraPriceData as $extraArray){
            $total = (int) \Arr::get($extraArray,'amount.amount');
            $totalWithCommission = (int) \Arr::get($extraArray,'price_data.price_with_commission');
            if($total && $totalWithCommission){
                $extraCommission =Money::EUR($totalWithCommission );
                $commission = $commission->add($extraCommission);
            }
        }

        return $commission;
    }

    public function getFormattedOtherTravellers(){

        $return = array();

        if($this->otherTravellers()){
            foreach ($this->otherTravellers as $traveller){
                $return[] = $traveller->full_name;
            }
        }

        $return = implode(',',$return);

        return $return;
    }

    public function getInvoiceData($param){

        $invoice_data = \DB::table('invoices')->where('reservation_id','=',$this->id)->first();

        $return = '';

        if(!empty($invoice_data)){
            switch ($param){
                case 'invoice_number':
                    $return = $invoice_data->invoice_id.'/'.$invoice_data->invoice_establishment.'/'.$invoice_data->invoice_device;
                    break;
                case 'zki':
                    $return = $invoice_data->zki;
                    break;
                case 'jir':
                    $return = $invoice_data->jir;
                    break;
            }
        }

        return $return;
    }

    public function getExtrasPriceStatesAttribute(){
        return \Arr::where($this->price_breakdown, fn($i) => $i['item']==='extra');
    }

    public function getTransferPriceStateAttribute():array
    {
        return \Arr::first(\Arr::where($this->price_breakdown, fn($i) => $i['item']==='transfer_price'));
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

    protected function priceBreakdown(): Attribute
    {
        return Attribute::make(
            get: function ($breakdown){
                $breakdown = json_decode($breakdown, true);
                foreach ($breakdown as $key => $breakdownItem){
                    if(\Arr::get($breakdownItem,'item') === 'extra'){
                        if(\Arr::get($breakdownItem,'model')){
                            $breakdown[$key]['model'] = Extra::make(\Arr::get($breakdownItem,'model'));
                        }
                    }
                }
                return $breakdown;
            },
        );
    }
}
