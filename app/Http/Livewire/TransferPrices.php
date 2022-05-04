<?php

namespace App\Http\Livewire;

use App\Models\Partner;
use App\Models\Route;
use App\Models\Transfer;
use Carbon\Carbon;
use Cknow\Money\Casts\MoneyDecimalCast;
use Cknow\Money\Casts\MoneyIntegerCast;
use Cknow\Money\Casts\MoneyStringCast;
use Cknow\Money\Money;
use Livewire\Component;
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
            $this->transfer = Transfer::with(['routes'=>function ($q){
                $q->where('partner_id',$this->partnerId);
            }])->find($this->transferId);

            $routes = $this->transfer->routes;

            foreach($routes as $r){
                $this->routePrice[$r->id] = Money::EUR($r->pivot->price)->format('fr_FR', null, \NumberFormatter::DECIMAL);
                $this->routeRoundTrip[$r->id] = $r->pivot->round_trip;
                $this->routePriceRoundTrip[$r->id] = Money::EUR($r->pivot->price_round_trip)->format('fr_FR', null, \NumberFormatter::DECIMAL);
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

    protected $rules = [
        //^\d+\,\d{2,2}$ MORA BIT sa divje decimale i zarez
        //^(\d+(?:[^\.]\d{2})?)$ mora bit sa zarezom ne morjau bit decimale

        'routePrice.*' => 'required|regex:/^\d+\,\d{2,2}$/|min:1',
        'routePriceRoundTrip.*' => 'regex:/^\d+\,\d{2,2}$/|min:1',
    ];


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

        $this->showToast('Updated', 'Two Way Data');
    }

    public function saveRoutePrice($routeId){

        $this->validate();

        if(empty($this->routePrice[$routeId])){
            return;
        }

        $money = Money::parse(str_replace(',','.',$this->routePrice[$routeId]),'EUR');

        \DB::table('route_transfer')->updateOrInsert(
            [
                'route_id'=>$routeId,
                'transfer_id'=>$this->transferId,
                'partner_id'=>$this->partnerId,
            ],
            [
                'price' =>  $money->getAmount()
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
