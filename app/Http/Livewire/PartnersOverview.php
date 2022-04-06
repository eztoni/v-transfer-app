<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PartnersOverview extends Component
{

    use WithPagination;

    public $search = '';
    public $partner;
    public $partnerModal;
    public $softDeleteModal;
    public $deleteId = '';

    protected function rules()
    {
        return [
            'partner.name'=>'required|max:255|min:2',
            'partner.destination_id'=>'required|numeric',
            'partner.email'=>'required|email',
            'partner.phone'=>'required|max:255',
        ];
    }
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function openPartnerModal(){
        $this->partnerModal = true;
    }

    public function closePartnerModal(){
        $this->partnerModal = false;
    }

    public function mount(){

        $this->partner = new Partner();
    }

    public function updatePartner($partnerId){
        $this->openPartnerModal();
        $this->partner = Partner::find($partnerId);
    }

    public function updatedPartnerDestinationId(){
        $this->partner->starting_point_id = null;
        $this->partner->ending_point_id = null;
    }


    public function addPartner(){
        $this->openPartnerModal();
        $this->partner = new Partner();
    }

    public function savePartnerData(){

        if(!Auth::user()->hasRole([User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN]))
            return;

        $this->partner->owner_id = Auth::user()->owner_id;

        $this->validate();
        $this->partner->save();
        $this->showToast('Saved','Partner Saved','success');
        $this->closePartnerModal();

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
        Partner::find($this->deleteId)->delete();
        $this->closeSoftDeleteModal();
        $this->showToast('Partner deleted','',);
    }
    //------------- Soft Delete End ---------

    public function render()
    {
        $destinations = Destination::all();
        $partners = Partner::search('name',$this->search)->paginate(10);
        return view('livewire.partners-overview',compact('partners','destinations'));
    }
}
