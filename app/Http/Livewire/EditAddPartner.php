<?php

namespace App\Http\Livewire;

use App\Models\Partner;
use Livewire\Component;

class EditAddPartner extends Component
{

    public $partner;
    public $editData =[];
    public $partnerId;

    protected $rules = [
        'editData.business_name' => 'required|max:255',
        'editData.contact_name' => 'required|max:255',
        'editData.contact_number' => 'required',
        'editData.email' => 'required|email',
        'editData.OIB' => 'required|min:11',
        'editData.beginning_of_contract' => 'required|date',
        'editData.end_of_contract' => 'required|date',
    ];


    public function mount($id =''){
        $this->partnerId = $id;
        if(!empty($this->partnerId)){

            $this->partner = Partner::findOrFail($this->partnerId);
            $this->editData = $this->partner->only([
                'business_name',
                'contact_name',
                'contact_number',
                'email',
                'OIB',
                'beginning_of_contract',
                'end_of_contract',
            ]);
        }else{
            $this->partner = new Partner();
        }

    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function savePartnerData(){
        $this->validate();

        $this->partner->fill($this->editData);
        $this->partner->save();
        $this->showToast('Spremljeno','Partner Spremljen','success');

    }

    public function render()
    {
        return view('livewire.edit-add-partner');
    }
}
