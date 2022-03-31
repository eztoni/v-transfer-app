<?php

namespace App\Http\Livewire;

use App\Models\AgeGroup;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AgeGroupOverview extends Component
{

    use WithPagination;

    public $search = '';
    public $ageGroup;
    public $ageGroupModal;

    protected $rules = [
        'ageGroup.name' => 'required|max:255',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function openAgeGroupModal(){
        $this->ageGroupModal = true;
    }

    public function closeAgeGroupModal(){
        $this->ageGroupModal = false;
    }

    public function updateAgeGroup($ageGroupId){
        $this->openAgeGroupModal();
        $this->ageGroup = AgeGroup::find($ageGroupId);
    }

    public function addAgeGroup(){
        $this->openAgeGroupModal();
        $this->ageGroup = new AgeGroup();
    }



    public function saveAgeGroupData(){

        $this->validate();
        $this->ageGroup->company_id = Auth::user()->company_id;
        $this->ageGroup->save();
        $this->showToast('Success','Age group saved, add some categories to it!');
        $this->closeAgeGroupModal();

    }

    public function render()
    {
        $ageGroups = AgeGroup::search('name',$this->search)->paginate(10);
        return view('livewire.age-group-overview',compact('ageGroups'));
    }
}
