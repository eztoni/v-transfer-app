<?php

namespace App\Http\Livewire;

use App\Models\Language;
use App\Models\Transfer;
use App\Models\Vehicle;
use Illuminate\Validation\Rule;
use Livewire\Component;

class TransferEdit extends Component
{
    public Transfer $transfer;
    public $companyLanguages = ['en'];
    public $vehicleId = null;
    public $transferName = [
        'en' => null
    ];
    protected function rules()
    {
        $ruleArray = [
            'transferName.en' => 'required|min:3',
            'vehicleId' => 'required|numeric|exists:App\Models\Vehicle,id',
        ];
        foreach ($this->companyLanguages as $lang) {
            if ($lang !== 'en') {
                $ruleArray['transferName.' . $lang] = 'nullable|min:3';
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

        $this->vehicleId = $this->transfer->vehicle->id;

        foreach ($this->companyLanguages as $lang) {
            $this->transferName[$lang] = $this->transfer->getTranslation('name', $lang, false);
        }
    }


    public function getVehiclesProperty()
    {
            return Vehicle::whereNull('transfer_id')->when($this->transfer->exists,function ($q){
                $q->orWhere('transfer_id',$this->transfer->id);
            })->get();
    }



    public function updatedTransferName()
    {
        $this->transfer->setTranslations('name', $this->transferName);
    }
    public function updated($field)
    {
        $this->validateOnly($field);
    }
    public function saveTransfer()
    {
        $this->transfer->setTranslations('name', $this->transferName);
        $this->transfer->save();

        $vehicles = $this->getVehiclesProperty();

        if(empty($this->vehicleId)){
            $this->addError('vehicleId','Please choose a vehicle.');
            return;
        }

        if($vehicles->isEmpty()){
            $this->addError('vehicleId','Vehicle already taken.');
            return;
        }
        $vehicle = Vehicle::findOrFail($this->vehicleId);
        Vehicle::where('transfer_id',$this->transfer->id)->update(['transfer_id' => null]);
        $this->transfer->vehicle()->save($vehicle);

        $this->showToast('Update successful');
    }



    public function render()
    {
        return view('livewire.transfer-edit');
    }
}
