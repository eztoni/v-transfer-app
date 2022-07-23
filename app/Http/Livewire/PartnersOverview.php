<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Partner;
use App\Models\User;
use App\Scopes\CompanyScope;
use App\Scopes\DestinationScope;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

class PartnersOverview extends Component
{
use Actions;

    use WithPagination;

    public $search = '';
    public $partner;
    public $partnerModal;
    public $softDeleteModal;
    public $deleteId = '';
    public $selectedDestinations = [];



    protected function rules()
    {
        return [
            'partner.name'=>'required|max:255|min:2',
            'partner.email'=>'required|email',
            'selectedDestinations'=>'required',
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



    public function updatedPartnerDestinationId(){
        $this->partner->starting_point_id = null;
        $this->partner->ending_point_id = null;
    }

    public function addPartner(){
        $this->openPartnerModal();
        $this->partner = new Partner();
        $this->selectedDestinations = [];
    }

    public function updatePartner($partnerId){

        $this->openPartnerModal();
        $this->partner = Partner::withoutGlobalScope(DestinationScope::class)->find($partnerId);
        $this->selectedDestinations = $this->partner?->destinations()?->pluck('id')->toArray();


    }

    public function savePartnerData(){

        if(!Auth::user()->hasRole([User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN]))
            return;

        $this->partner->owner_id = Auth::user()->owner_id;
        $this->validate();
        $this->partner->save();
        $this->partner->destinations()->sync($this->selectedDestinations);
        $this->notification()->success('Saved','Partner Saved');
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
        $this->notification()->success('Partner deleted','',);
    }
    //------------- Soft Delete End ---------

    public function render()
    {
        $destinations = Destination::all();
        $partners = Partner::withoutGlobalScope(DestinationScope::class)->with('destinations')->search('name',$this->search)->paginate(10);
        return view('livewire.partners-overview',compact('partners','destinations'));
    }
}
