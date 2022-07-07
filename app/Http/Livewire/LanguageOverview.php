<?php

namespace App\Http\Livewire;

use App\Models\Language;
use App\Models\User;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class LanguageOverview extends Component
{

    use WithPagination;

    public $search = '';
    public $language;
    public $languageModal;
    public $softDeleteModal;
    public $deleteId = '';

    protected $rules = [
        'language.name' => 'required|max:255',
        'language.language_code' => 'required|min:2|max:2|unique:languages,language_code',
    ];

    protected function rules()
    {
        return [
            'language.name' => 'required|max:255',
            'language.language_code' => 'required|min:2|max:2|unique:languages,language_code,'.$this->language->id,
        ];
    }

    public function mount()
    {
        $this->language = new Language();
    }


    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function openLanguageModal(){
        $this->languageModal = true;
    }

    public function closeLanguageModal(){
        $this->languageModal = false;
    }

    public function updateLanguage($languageId){
        $this->openLanguageModal();
        $this->language = Language::withoutGlobalScope(CompanyScope::class)->find($languageId);
    }

    public function addLanguage(){
        $this->openLanguageModal();
        $this->language = new Language();
    }

    public function saveLanguageData(){

        if(!Auth::user()->hasRole(User::ROLE_SUPER_ADMIN))
            return;

        $this->validate();
        $this->language->save();
        $this->showToast('Saved','Language Saved','success');
        $this->closeLanguageModal();

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
        Language::find($this->deleteId)->delete();
        $this->closeSoftDeleteModal();
        $this->showToast('Language deleted','',);
    }
    //------------- Soft Delete End -----------------


    public function render()
    {
        $languages = Language::withoutGlobalScope(CompanyScope::class)->search('name',$this->search)->paginate(10);
        return view('livewire.language-overview',compact('languages'));
    }
}
