<?php

namespace App\Http\Livewire;

use App\Models\Language;
use App\Models\Vehicle;
use Livewire\Component;

class VehicleEdit extends Component
{
    public Vehicle $vehicle;

    public $companyLanguages = ['en'];
    public $vehicleId = null;
    public $vehicleName = [
        'en' => null
    ];
    protected function rules()
    {
        $ruleArray = [
            'vehicleName.en' => 'required|min:3',
            'vehicle.type' => 'max:255',
            'vehicle.max_luggage' => 'required|integer',
            'vehicle.max_occ' => 'required|integer',
        ];
        foreach ($this->companyLanguages as $lang) {
            if ($lang !== 'en') {
                $ruleArray['vehicleName.' . $lang] = 'nullable|min:3';
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

        foreach ($this->companyLanguages as $lang) {
            $this->vehicleName[$lang] = $this->vehicle->getTranslation('name', $lang, false);
        }
    }

    public function updatedVehicleName()
    {
        $this->vehicle->setTranslations('name', $this->vehicleName);
    }
    public function updated($field)
    {
        $this->validateOnly($field);
    }

    public function saveVehicle()
    {
        $this->validate();

        $this->vehicle->setTranslations('name', $this->vehicleName);
        $this->vehicle->save();

        $this->showToast('Update successful');
    }

    public function render()
    {
        return view('livewire.vehicle-edit');
    }
}
