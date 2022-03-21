<?php

namespace App\Http\Livewire;

use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;

class VehicleOverview extends Component
{
    public $search = '';
    public $vehicle;
    public $vehicleModal;

    protected $rules = [
        'vehicle.name' => 'required|max:255',
        'vehicle.type' => 'max:255',
        'vehicle.max_luggage' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
        'vehicle.max_occ' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function openVehicleModal(){
        $this->vehicleModal = true;
    }

    public function closeVehicleModal(){
        $this->vehicleModal = false;
    }

    public function updateVehicle($vehicleId){
        $this->openVehicleModal();
        $this->vehicle = Vehicle::find($vehicleId);
    }

    public function addVehicle(){
        $this->openVehicleModal();
        $this->vehicle = new Vehicle();
    }

    public function saveVehicleData(){

        $this->validate();
        $this->vehicle->owner_id = Auth::user()->owner_id;
        $this->vehicle->save();
        $this->showToast('Success','Vehicle saved, add some info to it!');
        $this->closeVehicleModal();

    }

    public function render()
    {
        $vehicles = Vehicle::search('name',$this->search)->paginate(10);
        return view('livewire.vehicle-overview',compact('vehicles'));
    }

}
