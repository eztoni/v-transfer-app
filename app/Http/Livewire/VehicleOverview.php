<?php

namespace App\Http\Livewire;

use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;
use WireUi\Traits\Actions;

class VehicleOverview extends Component
{
use Actions;
    use WithPagination;

    public $search = '';
    public $vehicle;
    public $vehicleModal;
    public $vehicleType = [
        'en' => null
    ];

    protected $rules = [
        'vehicleType.en' => 'required|min:3',
        'vehicle.max_luggage' => 'required|integer',
        'vehicle.max_occ' => 'required|integer',
    ];

    public $fieldNames = [
        'vehicleType.en' => 'Type',
    ];

    public $messages = [
        'vehicleType.en.required' => 'The vehicle type is required.',
        'vehicleType.en.min:3' => 'The vehicle type must contain at least 3 characters.'
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

        $this->validate($this->rules, $this->messages, $this->fieldNames);

        $this->vehicle->destination_id = Auth::user()->destination_id;
        $this->vehicle->setTranslations('type', $this->vehicleType);
        $this->vehicle->save();
        $this->notification()->success('Success','Vehicle saved, add some info to it!');
        $this->closeVehicleModal();
        return \Redirect::to(route('vehicle-edit',['vehicleId'=>$this->vehicle->id]));
    }

    public function render()
    {
        $vehicles = Vehicle::search('type',$this->search)->with('media')->paginate(10);
        return view('livewire.vehicle-overview',compact('vehicles'));
    }

}
