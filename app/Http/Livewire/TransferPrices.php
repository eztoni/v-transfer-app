<?php

namespace App\Http\Livewire;

use App\Facades\EzMoney;
use App\Models\Partner;
use App\Models\Route;
use App\Models\Transfer;
use Carbon\Carbon;

use Cknow\Money\Money;
use Livewire\Component;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;
use Money\Parser\IntlLocalizedDecimalParser;
use function Symfony\Component\String\b;

class TransferPrices extends Component
{

    public $pivotModal;
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
        $this->partnerId = Partner::first()->id;
        $this->setModelPrices();

    }


    private function setModelPrices(): void
    {
        if( !empty($this->transferId) && !empty($this->partnerId)){
            $transfer = Transfer::with(['routes'=>function ($q){
                $q->where('partner_id',$this->partnerId);
            }])->find($this->transferId);

            $routes = $transfer->routes;

            foreach($routes as $r){

                $this->routePrice[$r->id] = EzMoney::format($r->pivot->price);
                $this->routeRoundTrip[$r->id] = $r->pivot->round_trip;
                $this->routePriceRoundTrip[$r->id] =EzMoney::format($r->pivot->price_round_trip);
            }
        }

    }

    public function updated($property): void
    {
        $this->validateOnly($property);
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

    protected $rules = array(

        'routePrice.*' => 'required|regex:'. \App\Services\Helpers\EzMoney::MONEY_REGEX.'|min:1',
        'routePriceRoundTrip.*' => 'regex:'. \App\Services\Helpers\EzMoney::MONEY_REGEX.'|min:1',
    );


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

        $this->showToast('Updated', 'Round Trip Data');
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

        $this->showToast('Saved', 'Route Price Saved');

    }

    public function saveRoutePriceRoundTrip($routeId){

        //$this->validate('routePriceRoundTrip.'.$routeId);


        if(empty($this->routePriceRoundTrip[$routeId])){
            $this->addError('routePriceRoundTrip.'.$routeId, 'The email field is invalid.');
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

        $this->showToast('Saved', 'Route Price Saved');

    }

    public function render()
    {
        $transfers = Transfer::all();

        return view('livewire.transfer-prices',compact('transfers'));
    }
}
