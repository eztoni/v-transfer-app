<?php

namespace App\Http\Livewire;

use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;

class VehicleOverview extends Component
{
    use WithPagination;

    public $search = '';
    public $vehicle;
    public $vehicleModal;
    public $vehicleName;

    protected $rules = [
        'vehicleName' => 'required|max:255',
        'vehicle.type' => 'max:255',
        'vehicle.max_luggage' => 'required|integer',
        'vehicle.max_occ' => 'required|integer',
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



    public function addVehicle(){
        $this->openVehicleModal();
        $this->vehicle = new Vehicle();
    }

    public function saveVehicleData(){

        $this->validate();
        $this->vehicle->name = $this->vehicleName;

        $this->vehicle->owner_id = Auth::user()->owner_id;
        $this->vehicle->save();
        $this->showToast('Success','Vehicle saved, add some info to it!');
        $this->closeVehicleModal();
        return \Redirect::to(route('vehicle-edit',['vehicle'=>$this->vehicle->id]));
    }

    public function render()
    {
        $vehicles = Vehicle::search('name',$this->search)->paginate(10);
        return view('livewire.vehicle-overview',compact('vehicles'));
    }

}
