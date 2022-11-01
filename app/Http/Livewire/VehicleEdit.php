<?php

namespace App\Http\Livewire;

use App\Models\Extra;
use App\Models\Language;
use App\Models\Vehicle;
use Livewire\Component;
use WireUi\Traits\Actions;

class VehicleEdit extends Component
{
use Actions;
    public Vehicle $vehicle;
    public $companyLanguages = ['en'];
    public $vehicleId = null;
    public $vehicleType = [
        'en' => null
    ];
    protected function rules()
    {
        $ruleArray = [
            'vehicleType.en' => 'required|min:3',
            'vehicle.max_luggage' => 'required|integer',
            'vehicle.max_occ' => 'required|integer',
        ];
        foreach ($this->companyLanguages as $lang) {
            if ($lang !== 'en') {
                $ruleArray['vehicleType.' . $lang] = 'nullable|min:3';
            }
        }
        return $ruleArray;
    }
    public function mount()
    {
        $this->instantiateComponentValues();

    }
    public function instantiateComponentValues()
    {
        $this->companyLanguages = Language::all()->pluck('language_code')->toArray();
        $this->vehicle = Vehicle::find($this->vehicleId);

        foreach ($this->companyLanguages as $lang) {
            $this->vehicleType[$lang] = $this->vehicle->getTranslation('type', $lang, false);
        }
    }
    public function getAllVehiclesForSelectProperty()
    {
        return Vehicle::all()->transform(function (Vehicle $item){
            return ['id'=>(string) $item->id,
                'name'=>$item->type];
        })->toArray();
    }
    public function updatedVehicleId()
    {
        $this->instantiateComponentValues();
    }
    public function updatedvehicleType()
    {
        $this->vehicle->setTranslations('type', $this->vehicleType);
    }
    public function updated($field)
    {
        $this->validateOnly($field);
    }

    public function saveVehicle()
    {
        $this->validate();

        $this->vehicle->setTranslations('type', $this->vehicleType);
        $this->vehicle->save();

        $this->notification()->success('Update successful');
    }

    public function render()
    {
        return view('livewire.vehicle-edit');
    }
}
