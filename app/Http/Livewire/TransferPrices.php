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
    public $transferId = null;
    public $showSearch = true;
    public $partnerId = 0;


    public function mount(): void
    {

        $first = Transfer::first();
        $this->transferId = $first->id ?? null;
        $this->partnerId = Partner::first()?->id;
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
                $this->routeDateFrom[$r->id] = $r->pivot->date_from;
                $this->routeDateTo[$r->id] = $r->pivot->date_from;
                $this->routeCommissionPercentage[$r->id] = $r->pivot->commission;
                $this->routeDiscountPercentage[$r->id] = $r->pivot->discount;
                $this->routePriceWithDiscount[$r->id] = EzMoney::format(GetRouteDiscount::run($this->transfer,false,$r->id));
                $this->routeRoundTripPriceWithDiscount[$r->id] = EzMoney::format(GetRouteDiscount::run($this->transfer,true,$r->id));
                $this->routePriceCommission[$r->id] = EzMoney::format(GetRouteCommission::run($this->transfer,false,$r->id));
                $this->routeRoundTripPriceCommission[$r->id] = EzMoney::format(GetRouteCommission::run($this->transfer,true,$r->id));
            }
        }
    }

    public function updateDiscountPrices($rId){
        $this->routePriceWithDiscount[$rId] = EzMoney::format(GetRouteDiscount::run($this->transfer,false,$rId,$this->routeDiscountPercentage[$rId],$this->routePrice[$rId]));
        $this->routeRoundTripPriceWithDiscount[$rId] = EzMoney::format(GetRouteDiscount::run($this->transfer,true,$rId,$this->routeDiscountPercentage[$rId],$this->routePriceRoundTrip[$rId]));
    }

    public function updateCommissionPrices($rId){
        $this->routePriceCommission[$rId] = EzMoney::format(GetRouteCommission::run($this->transfer,false,$rId,$this->routeCommissionPercentage[$rId],$this->routePrice[$rId]));
        $this->routeRoundTripPriceCommission[$rId] = EzMoney::format(GetRouteCommission::run($this->transfer,true,$rId,$this->routeCommissionPercentage[$rId],$this->routePriceRoundTrip[$rId]));
    }

    public function updated($property): void
    {

        if(Str::contains($property, 'routeDiscountPercentage')){
            $routeId = explode('.',$property)[1];
            $this->updateDiscountPrices($routeId);
        }

        if(Str::contains($property, 'routeCommissionPercentage')){
            $routeId = explode('.',$property)[1];
            $this->updateCommissionPrices($routeId);
        }


        $this->validateOnly($property,$this->rules(),[],$this->fieldNames);
    }

    public function updateRoutePrice($rId){
        $this->routePriceWithDiscount[$rId] = EzMoney::format(GetRouteDiscount::run($this->transfer,false,$rId,$this->routeDiscountPercentage[$rId],$this->routePrice[$rId]));
        $this->routePriceCommission[$rId] = EzMoney::format(GetRouteCommission::run($this->transfer,false,$rId,$this->routeCommissionPercentage[$rId],$this->routePrice[$rId]));
    }

    public function updateRoutePriceRoundTrip($rId){
        $this->routeRoundTripPriceWithDiscount[$rId] = EzMoney::format(GetRouteDiscount::run($this->transfer,true,$rId,$this->routeDiscountPercentage[$rId],$this->routePriceRoundTrip[$rId]));
        $this->routeRoundTripPriceCommission[$rId] = EzMoney::format(GetRouteCommission::run($this->transfer,true,$rId,$this->routeCommissionPercentage[$rId],$this->routePriceRoundTrip[$rId]));
    }

    public function updatedTransferId(): void
    {
        $this->routePrice =  [];
        $this->routePriceRoundTrip = [];
        $this->routeRoundTrip = [];
        $this->setModelPrices();
    }
    public function updatedPartnerId(): void
    {
        $this->routePrice =  [];
        $this->routePriceRoundTrip = [];
        $this->routeRoundTrip = [];
        $this->setModelPrices();
    }

    public $fieldNames = [
        'routeCommissionPercentage.*' => 'commission Percentage',
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


    public function getTransferRoutesProperty(){
        if($this->transferId > 0){
            return Transfer::find($this->transferId)->routes;
        }
        return collect();
    }

    public function getRoutesProperty(){
        return  Route::with(['startingPoint','endingPoint'])->get();
    }

    public function saveRoundTrip($routeId){


        $saved = $this->routeRoundTrip[$routeId];

        \DB::table('route_transfer')->updateOrInsert(
            [
                'route_id'=>$routeId,
                'transfer_id'=>$this->transferId,
                'partner_id'=>$this->partnerId,
            ],
            [
                'round_trip' => $saved,
            ]
        );

        $this->notification()->success('Updated', 'Round Trip Data');
    }

    public function saveRoutePrice($routeId){

        $this->validate();

        if(empty($this->routePrice[$routeId])){
            return;
        }


        \DB::table('route_transfer')->updateOrInsert(
            [
                'route_id'=>$routeId,
                'transfer_id'=>$this->transferId,
                'partner_id'=>$this->partnerId,
            ],
            [
                'price' =>  EzMoney::parseForDb($this->routePrice[$routeId])
            ]
        );

        $this->notification()->success('Saved', 'Route Price Saved');

    }

    public function saveRoutePriceRoundTrip($routeId){

        if(empty($this->routePriceRoundTrip[$routeId])){
            $this->addError('routePriceRoundTrip.'.$routeId, 'The round trip price field is empty.');
            return;
        }

        if(preg_match(\App\Services\Helpers\EzMoney::MONEY_REGEX,$this->routePriceRoundTrip[$routeId]) <= 0){
            $this->addError('routePriceRoundTrip.'.$routeId, 'The round trip price field is invalid.');
            $this->notification()->success('Not Saved', 'Round Trip Price Invalid Value');
            return;
        }

        $money = Money::parse(str_replace(',','.',$this->routePriceRoundTrip[$routeId]),'EUR');

        \DB::table('route_transfer')->updateOrInsert(
            [
                'route_id'=>$routeId,
                'transfer_id'=>$this->transferId,
                'partner_id'=>$this->partnerId,
            ],
            [
                'price_round_trip' =>  $money->getAmount()
            ]
        );

        $this->notification()->success('Saved', 'Round Trip Price Saved');

    }

    public function save($routeId){

        $this->validate($this->singelSaveRules($routeId), [], $this->fieldNames);

        if(empty($this->routePrice[$routeId])){
            return;
        }

        $roundTripPrice = 0;
        if($this->routePriceRoundTrip[$routeId]){
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
                'date_from' => Carbon::create($this->routeDateFrom[$routeId]),
                'date_to' => Carbon::create($this->routeDateTo[$routeId]),
                'tax_level' => $this->routeTaxLevel[$routeId],
                'calculation_type' => $this->routeCalculationType[$routeId]
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
