<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Destinations extends Component
{

    public $search = '';
    public $user;
    public $destination;
    public $destinationModal;
    public $softDeleteModal;
    public $deleteId = '';
    public $editData =[];


    protected $rules = [
        'editData.name' => 'required|max:255',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount(){
        $this->user = Auth::user();
    }

    public function openDestinationModal(){
        $this->destinationModal = true;
    }

    public function closeDestinationModal(){
        $this->destinationModal = false;
        $this->editData = [];
    }

    public function saveDestinationData(){
        $this->validate();

        $this->destination = new Destination();
        $this->destination->fill($this->editData);
        $this->destination->company_id = $this->user->company_id;
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
