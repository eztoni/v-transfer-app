<?php

namespace App\Http\Livewire;

use App\Models\Partner;
use App\Models\Route;
use App\Models\Transfer;
use Carbon\Carbon;
use Livewire\Component;
use function Symfony\Component\String\b;

class TransferPrices extends Component
{

    public $pivotModal;
    public $routePrice = [];
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
                $this->routePrice[$r->id] = $r->pivot->price;
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

        $this->setModelPrices();
    }
    public function updatedPartnerId(): void
    {
        $this->routePrice =  [];

        $this->setModelPrices();
    }
    protected $rules = [
        'routePrice.*' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/|min:1',
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
                'price' =>  $this->routePrice[$routeId]
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
