<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Language;
use App\Models\Transfer;
use App\Models\Vehicle;
use Illuminate\Validation\Rule;
use Livewire\Component;
use WireUi\Traits\Actions;

class TransferEdit extends Component
{
use Actions;
    public Transfer $transfer;
    public $companyLanguages = ['en'];
    public $vehicleId = null;
    public $destinationId = null;
    public $transferName = [
        'en' => null
    ];
    protected function rules()
    {
        $ruleArray = [
            'transferName.en' => 'required|min:3',
            'destinationId' => 'required|numeric|exists:App\Models\Destination,id',
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
        $this->destinationId = $this->transfer->destination_id;
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
        $destination = Destination::findOrFail($this->destinationId);

        $this->transfer->destination_id = $destination->id;

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

        Vehicle::where('transfer_id',$this->transfer->id)->update(['transfer_id' => null]);
        $vehicle = Vehicle::findOrFail($this->vehicleId);
        $vehicle->transfer_id = $this->transfer->id;
        $vehicle->save();
        $this->notification()->success('Update successful');
    }



    public function render()
    {
        return view('livewire.transfer-edit');
    }
}
