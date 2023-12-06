<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Language;
use App\Models\Transfer;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

class TransferOverview extends Component
{
use Actions;

    use WithPagination;

    public $search = '';
    public $transfer;
    public $vehicleId;
    public $duplicateTransferID;
    public $transferModal;
    public $transferName;
    public $destinationId;
    public $companyLanguages = ['en'];
    public $transferCopyName = [
        'en' => null
    ];
    public $duplicateTransferModal = false;


    protected $rules = [
        'transferName' => 'required|max:255',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function openTransferModal(){
        $this->transferModal = true;
    }

    public function closeTransferModal(){
        $this->transferModal = false;
    }

    public function addTransfer(){
        $this->openTransferModal();
        $this->transfer = new Transfer();
    }

    public function getVehiclesProperty()
    {
        if($this->transfer) {
            return Vehicle::whereNull('transfer_id')->when($this->transfer->exists, function ($q) {
                $q->orWhere('transfer_id', $this->transfer->vehicle->id);
            })->get()->prepend(['type'=>'Select vehicle','id'=>null],null);
        }else{
            return Vehicle::whereNull('transfer_id')->get();
        }

        return collect();
    }


    public function saveTransferData(){
        $this->validate();
        $this->transfer->owner_id = Auth::user()->owner_id;

        $vehicles = $this->getVehiclesProperty();

        if(empty($this->vehicleId)){
            $this->addError('vehicleId','Please choose a vehicle.');
            return false;
        }

        if($vehicles->isEmpty()){
            $this->addError('vehicleId','Vehicle already taken.');
            return false;
        }

        $destination_id = Auth::user()->destination_id;
        $this->transfer->destination_id = $destination_id;

        $this->transfer->name = $this->transferName;

        $this->transfer->save();
        $this->transfer->vehicle()->save(Vehicle::findOrFail($this->vehicleId));
        $this->notification()->success('Success','Transfer saved, add some info to it!');
        $this->closeTransferModal();

        return Redirect::to(route('transfer-edit',['transferId'=>$this->transfer->id]));

    }

    public function showDuplicateTransferModal($transfer_id){

        $tnr = Transfer::where('id',$transfer_id)->get()->first();

        $this->duplicateTransferID = $transfer_id;
        $this->transferCopyName = $tnr->getTranslations('name');

        $this->duplicateTransferModal = true;

    }
    public function hideDuplicateTransferModal(){
        $this->duplicateTransferModal = false;
    }

    public function mount(){
        $this->companyLanguages = Language::all()->pluck('language_code')->toArray();
    }
    public function render()
    {
        $transfers = Transfer::with(['destination','media'])->search('name',$this->search)->paginate(10);

        return view('livewire.transfer-overview',compact('transfers',));
    }

    public function duplicateTransfer(){



        $tnr = Transfer::find($this->duplicateTransferID)->first();

        $copyTransfer = $tnr->replicate();

        $copyTransfer->setTranslations('name',$this->transferCopyName);

        $copyTransfer->save();

        $vehicle = Vehicle::where('transfer_id',$this->duplicateTransferID)->first();

        $copyVehicle = $vehicle->replicate();

        $copyVehicle->transfer_id = $copyTransfer->id;

        $copyVehicle->save();

        $this->notification()->success('Success','Transfer duplicated!');

        $this->hideDuplicateTransferModal();
    }
}
