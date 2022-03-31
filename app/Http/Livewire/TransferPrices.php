<?php

namespace App\Http\Livewire;

use App\Models\Route;
use App\Models\Transfer;
use Livewire\Component;

class TransferPrices extends Component
{

    public $pivotModal;
    public $routePrice = [];
    public $routeSaveButton = [];
    public $routeId;
    public $price;
    public $transferId = null;
    public $showSearch = true;

    public function mount()
    {
       $this->setModelPrices();
    }


    private function setModelPrices(){
        if($this->transferId > 0){
            $this->transfer = Transfer::with('routes')->find($this->transferId);

            $routes = $this->transfer->routes;
            foreach($routes as $r){
                $this->routePrice[$r->id] = $r->pivot->price;
            }
        }
    }

    public function updated($property)
    {
        $this->validateOnly($property);
    }

    public function updatedTransferId()
    {
        $this->routePrice =  [];

        $this->setModelPrices();
    }

    protected $rules = [
        'routeId' => 'required|max:255',
        'price' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
        'routePrice.*' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
    ];

    public function openPivotModal(){
        $this->pivotModal = true;
    }

    public function closePivotModal(){
        $this->pivotModal = false;
        $this->routeId = null;
        $this->price = null;
    }

    public function getTransferRoutesProperty(){
        if($this->transferId > 0){
            return Transfer::find($this->transferId)->routes;
        }
        return collect();
    }

    public function getRoutesProperty(){
        return  Route::all();
    }

    public function saveRoutePrice($routeId){
        dd($this->validate());

        $transfer = Transfer::findOrFail($this->transferId);
        $transfer->routes()->syncWithPivotValues($routeId , ['price' => $this->routePrice[$routeId]]);
        $transfer->save();
        $this->showToast('Saved', 'Route Price Saved', 'success');

    }

    public function savePivotData(){
        $this->validate();
        $transfer = Transfer::findOrFail($this->transferId);

        $transfer->routes()->attach($this->routeId,['price' => $this->price]);
        $transfer->save();
        $this->setModelPrices();
        $this->closePivotModal();
        $this->showToast('Saved', 'Route Saved', 'success');

    }

    public function render()
    {
        $transfers = Transfer::all();

        return view('livewire.transfer-prices',compact('transfers'));
    }
}
