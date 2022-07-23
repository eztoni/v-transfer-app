<?php

namespace App\Http\Livewire;

use App\Models\Destination;
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
    public $transferModal;
    public $transferName;
    public $destinationId;



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
            })->get()->prepend(['name'=>'Select vehicle','id'=>null],null);
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

        return Redirect::to(route('transfer-edit',['transfer'=>$this->transfer->id]));

    }


    public function render()
    {
        $transfers = Transfer::with('destination')->search('name',$this->search)->paginate(10);


        return view('livewire.transfer-overview',compact('transfers'));
    }
}
