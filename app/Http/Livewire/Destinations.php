<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Destinations extends Component
{

    public $search = '';
    public $destination;
    public $destinationModal;
    public $softDeleteModal;
    public $deleteId = '';

    protected $rules = [
        'destination.name' => 'required|max:255',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function openDestinationModal(){
        $this->destinationModal = true;
    }

    public function closeDestinationModal(){
        $this->destinationModal = false;
    }

    public function updateDestination($destinationId){
        $this->openDestinationModal();
        $this->destination = Destination::find($destinationId);
    }

    public function addDestination(){
        $this->openDestinationModal();
        $this->destination = new Destination();
    }

    public function saveDestinationData(){
        $this->validate();

        if(!Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN))
            return;

        if($this->destination->exists && $this->destination->company_id != Auth::user()->company_id )
            return;

        $this->destination->company_id = Auth::user()->company_id;
        $this->destination->save();
        $this->showToast('Saved','Destination Saved','success');
        $this->closeDestinationModal();

    }

    //------------ Soft Delete ------------------
    public function openSoftDeleteModal($id){
        $this->deleteId = $id;
        $this->softDeleteModal = true;
    }

    public function closeSoftDeleteModal(){
        $this->deleteId = '';
        $this->softDeleteModal = false;
    }

    public function softDelete(){
        Destination::find($this->deleteId)->delete();
        $this->closeSoftDeleteModal();
        $this->showToast('Destination deleted','',);
    }
    //------------- Soft Delete End -----------------


    public function render()
    {
        $destinations = Destination::search('name',$this->search)->paginate(10);
        return view('livewire.destinations', compact('destinations'));

    }
}
