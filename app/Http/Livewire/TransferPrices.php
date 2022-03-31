<?php

namespace App\Http\Livewire;

use App\Models\Route;
use App\Models\Transfer;
use Livewire\Component;

class TransferPrices extends Component
{

    public $transfer;
    public $pivotModal;
    public $routePrice = [];
    public $routeSaveButton = [];
    public $routeId;
    public $price;
    public $transferId = null;
    public $transferRoutes = null;
    public $showSearch = true;

    public function mount()
    {
        if($this->transferId > 0){
            $this->transfer = Transfer::with('routes')->find($this->transferId);

            $routes = $this->transfer->routes;
            foreach($routes as $r){
                $this->routePrice[$r->id] = $r->pivot->price;
            }
        }
    }

    public function updatedTransferId()
    {
        if($this->transferId > 0){
            $this->transfer = Transfer::with('routes')->find($this->transferId);

            $routes = $this->transfer->routes;
            foreach($routes as $r){
                $this->routePrice[$r->id] = $r->pivot->price;
            }
        }
    }

    protected $rules = [
        'routeId' => 'required|max:255',
        'price' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
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
            $this->transfer = Transfer::with('routes')->find($this->transferId);
            $routes = $this->transfer->routes;
            return $routes;
        }
    }

    public function getRoutesProperty(){
        $id = $this->transferId;
        $routes = Route::whereDoesntHave('transfers', function($q) use ($id){
            $q->where('transfer_id', $id);
        })->get();

        return  $routes;
    }

    public function saveRoutePrice($routeId){

        $this->transfer->routes()->syncWithPivotValues($routeId , ['price' => $this->routePrice[$routeId]]);
        $this->transfer->save();
        $this->showToast('Saved', 'Route Price Saved', 'success');

    }

    public function savePivotData(){
        $this->validate();
        $this->transfer->routes()->attach($this->routeId,['price' => $this->price]);
        $this->transfer->save();
        $this->closePivotModal();
        $this->showToast('Saved', 'Route Saved', 'success');

    }

    public function render()
    {

        $transfers = Transfer::all();

        return view('livewire.transfer-prices',compact('transfers'));
    }
}
