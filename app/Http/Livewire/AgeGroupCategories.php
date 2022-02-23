<?php

namespace App\Http\Livewire;

use App\Models\AgeCategory;
use App\Models\AgeGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AgeGroupCategories extends Component
{

    public $search = '';
    public $ageCategory;
    public AgeGroup $ageGroup;
    public $ageCategoryModal;


    protected $rules = [
        'ageCategory.category_name' => 'required',
        'ageCategory.age_from' => 'required|numeric|lt:ageCategory.age_to',
        'ageCategory.age_to' => 'required|numeric|gt:ageCategory.age_from',
    ];

    public function updated($propertyName)
    {
        if($propertyName == 'ageCategory.age_to'){
            $this->validateOnly('ageCategory.age_from');
            $this->validateOnly('ageCategory.age_to');
        }

        $this->validateOnly($propertyName);

    }

    public function openAgeCategoryModal(){
        $this->ageCategoryModal = true;
    }

    public function closeAgeCategoryModal(){
        $this->ageCategoryModal = false;
    }

    public function updateAgeCategory($ageCategoryId){
        $this->openAgeCategoryModal();
        $this->ageCategory = AgeCategory::find($ageCategoryId);
    }

    public function addAgeCategory(){
        $this->openAgeCategoryModal();
        $this->ageCategory = new AgeCategory();
    }

    public function validateOverlaps()
    {



        $ageCategoriesOverlap = AgeCategory::whereAgeGroupId($this->ageGroup->id)->where('age_from','<',$this->ageCategory->age_to)
            ->where('age_to','>',$this->ageCategory->age_from )->get();
        if($ageCategoriesOverlap->isNotEmpty() AND $ageCategoriesOverlap->first()->id != $this->ageCategory->id ){
            //dd($ageCategoriesOverlap->first()->id);
            //dd($this->ageCategory->id);
            $this->addError('ageOverlapError','This age overlaps with other ages');
            return false;
        }


        return true;
    }

    public function saveAgeCategoryData(){
        $this->validate();

        if($this->validateOverlaps() == false){
            return false;
        }


        $this->ageCategory->age_group_id = $this->ageGroup->id;
        $this->ageCategory->save();
        $this->showToast('Saved','Age Category Saved','success');
        $this->closeAgeCategoryModal();
    }

    public function render()
    {
        $ageCategories = AgeCategory::where('age_group_id','=',$this->ageGroup->id)->get();
        $existingCategories = AgeCategory::whereAgeGroupId($this->ageGroup->id)->pluck('category_name');
        return view('livewire.age-group-categories',compact('ageCategories','existingCategories'));
    }
}
