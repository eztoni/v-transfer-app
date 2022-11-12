<?php

namespace App\Http\Livewire;

use App\Actions\TransferPrice\GetRouteCommission;
use App\Actions\TransferPrice\GetRouteDiscount;
use App\Actions\TransferPrice\GetRouteOneWayPriceDiscount;
use App\Actions\TransferPrice\GetRouteRoundTripPriceDiscount;
use App\Facades\EzMoney;
use App\Models\Partner;
use App\Models\Route;
use App\Models\Transfer;
use Carbon\Carbon;

use Cknow\Money\Money;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;
use Money\Parser\IntlLocalizedDecimalParser;
use WireUi\Traits\Actions;
use function Symfony\Component\String\b;

class TransferPrices extends Component
{
    use Actions;

    public $pivotModal;
    public $transfer;
    public $routeDateFrom = [];
    public $routeDateTo = [];
    public $routeTaxLevel = [];
    public $routeCalculationType = [];
    public $routeCommissionPercentage = [];
    public $routeDiscountPercentage = [];
    public $routePriceWithDiscount = [];
    public $routeRoundTripPriceWithDiscount = [];
    public $routePriceCommission = [];
    public $routeRoundTripPriceCommission = [];
    public $routePrice = [];
    public $routeRoundTrip = [];
    public $routePriceRoundTrip = [];
    public $routeSaveButton = [];
    public $transferId;
    public $showSearch = true;
    public $partnerId = 0;


    public function singelFieldNames($routeId){
        return [
            'routeCommissionPercentage.'.$routeId => 'commission Percentage',
            'routeDiscountPercentage.'.$routeId => 'discount Percentage',
            'routePrice.'.$routeId => 'price',
            'routePriceRoundTrip.'.$routeId => 'round trip price',
            'routeTaxLevel.'.$routeId => 'route tax level',
            'routeCalculationType.'.$routeId => 'route calculation type',
            'routeDateFrom.'.$routeId => 'date from',
            'routeDateTo.'.$routeId => 'date to'
        ];
    }

    public $fieldNames = [
        'routeCommissionPercentage.*' => 'commission Percentage',
        'routeDiscountPercentage.*' => 'discount Percentage',
        'routePrice.*' => 'price',
        'routePriceRoundTrip.*' => 'round trip price',
        'routeTaxLevel.*' => 'route tax level',
        'routeCalculationType.*' => 'route calculation type',
        'routeDateFrom.*' => 'date from',
        'routeDateTo.*' => 'date to'
    ];

    public function rules(){

        return [
            'routeCommissionPercentage.*' => 'required|min:0|max:100|integer',
            'routeDiscountPercentage.*' => 'required|min:0|max:100|integer',
            'routePrice.*' => 'required|min:1|regex:'. \App\Services\Helpers\EzMoney::MONEY_REGEX,
            'routePriceRoundTrip.*' => 'min:1|regex:'. \App\Services\Helpers\EzMoney::MONEY_REGEX,
            'routeTaxLevel.*' => 'required',
            'routeCalculationType.*' => 'required',
            'routeDateFrom.*' => 'required|date',
            'routeDateTo.*' => 'required|date|after_or_equal:routeDateFrom.*',
        ];
    }

    public function singelSaveRules($routeId){

        return [
            'routeCommissionPercentage.'.$routeId => 'required|min:0|max:100|integer',
            'routeDiscountPercentage.'.$routeId => 'required|min:0|max:100|integer',
            'routePrice.'.$routeId => 'required|min:1|regex:'. \App\Services\Helpers\EzMoney::MONEY_REGEX,
            'routePriceRoundTrip.'.$routeId  => 'min:1|regex:'. \App\Services\Helpers\EzMoney::MONEY_REGEX,
            'routeTaxLevel.'.$routeId  => 'required',
            'routeCalculationType.'.$routeId  => 'required',
            'routeDateFrom.'.$routeId  => 'required|date',
            'routeDateTo.'.$routeId  => 'required|date|after_or_equal:routeDateFrom.'.$routeId,
        ];
    }

    public function mount(): void
    {
        $this->partnerId = Partner::first()?->id;
        $this->setModelPrices();
    }

    public function updatedPartnerId(): void
    {
        $this->routePrice =  [];
        $this->routePriceRoundTrip = [];
        $this->routeRoundTrip = [];
        $this->routeDateFrom = [];
        $this->routeDateTo = [];
        $this->routeTaxLevel = [];
        $this->routeCalculationType = [];
        $this->routeCommissionPercentage = [];
        $this->routeDiscountPercentage = [];
        $this->routePriceWithDiscount = [];
        $this->routeRoundTripPriceWithDiscount = [];
        $this->routePriceCommission = [];
        $this->routeRoundTripPriceCommission = [];
        $this->setModelPrices();
    }

    public function updatedTransferId(): void
    {
        $this->routePrice =  [];
        $this->routePriceRoundTrip = [];
        $this->routeRoundTrip = [];
        $this->setModelPrices();
    }

    private function setModelPrices(): void
    {
        if( !empty($this->transferId) && !empty($this->partnerId)){
            $this->transfer = Transfer::with(['routes'=>function ($q){
                $q->where('partner_id',$this->partnerId);
            }])->find($this->transferId);
            $routes = $this->transfer->routes;

            foreach($routes as $r){

                $this->routePrice[$r->id] = EzMoney::format($r->pivot->price);
                $this->routeRoundTrip[$r->id] = $r->pivot->round_trip;
                $this->routePriceRoundTrip[$r->id] =EzMoney::format($r->pivot->price_round_trip);
                $this->routeCalculationType[$r->id] = $r->pivot->calculation_type;
                $this->routeTaxLevel[$r->id] = $r->pivot->tax_level;
                $this->routeDateFrom[$r->id] = Carbon::make($r->pivot->date_from)?->format('d.m.Y') ?? '';
                $this->routeDateTo[$r->id] = Carbon::make($r->pivot->date_to)?->format('d.m.Y') ?? '';
                $this->routeCommissionPercentage[$r->id] = $r->pivot->commission ?: 0;
                $this->routeDiscountPercentage[$r->id] = $r->pivot->discount ?: 0;
                $this->routePriceWithDiscount[$r->id] = EzMoney::format(GetRouteDiscount::run($this->transfer,false,$r->id,$r->pivot->discount,EzMoney::format($r->pivot->price)));
                $this->routeRoundTripPriceWithDiscount[$r->id] = EzMoney::format(GetRouteDiscount::run($this->transfer,true,$r->id,$r->pivot->discount,EzMoney::format($r->pivot->price),EzMoney::format($r->pivot->price_round_trip)));
                $this->routePriceCommission[$r->id] = EzMoney::format(GetRouteCommission::run($this->transfer,false,$r->id,$r->pivot->commission,EzMoney::format($r->pivot->price)));
                $this->routeRoundTripPriceCommission[$r->id] = EzMoney::format(GetRouteCommission::run($this->transfer,true,$r->id,$r->pivot->commission,EzMoney::format($r->pivot->price),EzMoney::format($r->pivot->price_round_trip)));
            }

        }
    }

    //Update readonly discount fields Price With Discount and Round Trip Price with discount
    public function updateDiscountPrices($rId){
        $this->routePriceWithDiscount[$rId] = EzMoney::format(GetRouteDiscount::run($this->transfer,false,$rId,Arr::get($this->routeDiscountPercentage,$rId),Arr::get($this->routePrice,$rId)));
        $this->routeRoundTripPriceWithDiscount[$rId] = EzMoney::format(GetRouteDiscount::run($this->transfer,true,$rId,Arr::get($this->routeDiscountPercentage,$rId),Arr::get($this->routePrice,$rId),Arr::get($this->routePriceRoundTrip,$rId)));
    }

    //Update readonly commission fields Price With Commission and Round Trip Price with commission
    public function updateCommissionPrices($rId){
        $this->routePriceCommission[$rId] = EzMoney::format(GetRouteCommission::run($this->transfer,false,$rId,Arr::get($this->routeCommissionPercentage,$rId),Arr::get($this->routePrice,$rId)));
        $this->routeRoundTripPriceCommission[$rId] = EzMoney::format(GetRouteCommission::run($this->transfer,true,$rId,Arr::get($this->routeCommissionPercentage,$rId),Arr::get($this->routePrice,$rId),Arr::get($this->routePriceRoundTrip,$rId)));
    }

    public function updated($property): void
    {

        //When Discount Percentage is updated
        if(Str::contains($property, 'routeDiscountPercentage')){
            $routeId = explode('.',$property)[1];

            if($this->routeDiscountPercentage[$routeId] > 100){
                $this->routeDiscountPercentage[$routeId] = 100;
            }

            $this->updateDiscountPrices($routeId);
        }

        //When Commission Percentage is updated
        if(Str::contains($property, 'routeCommissionPercentage')){
            $routeId = explode('.',$property)[1];

            if($this->routeCommissionPercentage[$routeId] > 100){
                $this->routeCommissionPercentage[$routeId] = 100;
            }

            $this->updateCommissionPrices($routeId);
        }

        $this->validateOnly($property,$this->rules(),[],$this->fieldNames);
    }

    //When Trip Price is updated
    public function updateRoutePrice($rId){
        $this->updateDiscountPrices($rId);
        $this->updateCommissionPrices($rId);
    }

    //When Round Trip Price is updated
    public function updateRoutePriceRoundTrip($rId){
        $this->updateDiscountPrices($rId);
        $this->updateCommissionPrices($rId);
    }

    public function getTransferRoutesProperty(){
        if($this->transferId > 0){
            return Transfer::find($this->transferId)->routes;
        }
        return collect();
    }

    public function getRoutesProperty(){
        return  Route::with(['startingPoint','endingPoint'])->get();
    }



    public function save($routeId){

        $this->validate($this->singelSaveRules($routeId), [], $this->singelFieldNames($routeId));

        if(empty($this->routePrice[$routeId])){
            return;
        }

        $roundTripPrice = 0;
        if(Arr::get($this->routePriceRoundTrip,$routeId)){
            $roundTripPrice = EzMoney::parseForDb($this->routePriceRoundTrip[$routeId]);
        }

        \DB::table('route_transfer')->updateOrInsert(
            [
                'route_id'=>$routeId,
                'transfer_id'=>$this->transferId,
                'partner_id'=>$this->partnerId,
            ],
            [
                'price' =>  EzMoney::parseForDb($this->routePrice[$routeId]),
                'price_round_trip' => $roundTripPrice,
                'commission' => $this->routeCommissionPercentage[$routeId],
                'discount' => $this->routeDiscountPercentage[$routeId],
                'date_from' => Carbon::createFromFormat('d.m.Y',$this->routeDateFrom[$routeId]),
                'date_to' => Carbon::createFromFormat('d.m.Y',$this->routeDateTo[$routeId]),
                'tax_level' => $this->routeTaxLevel[$routeId],
                'calculation_type' => $this->routeCalculationType[$routeId],
                'round_trip' => Arr::get($this->routeRoundTrip,$routeId) ?: false

            ]
        );

        $this->notification()->success('Saved', 'Route Price Saved');

    }


    public function render()
    {
        $transfers = Transfer::all();

        return view('livewire.transfer-prices',compact('transfers'));
    }
}
