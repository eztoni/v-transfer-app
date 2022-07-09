<?php

namespace App\Http\Livewire;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyOverview extends Component
{

    use WithPagination;

    public $search = '';
    public $company;
    public $companyModal;
    public $softDeleteModal;
    public $deleteId = '';

    protected $rules = [
        'company.name' => 'required|max:255',
        'company.zip' => 'required|max:255',
        'company.country_id' => 'required',
        'company.city' => 'required|max:255',
        'company.contact' => 'required|max:255',
        'company.email' => 'required|email|max:255',
    ];

    public function mount()
    {
        $this->company = new Company();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function openCompanyModal(){
        $this->companyModal = true;
    }

    public function closeCompanyModal(){
        $this->companyModal = false;
    }

    public function updateCompany($companyId){
        $this->openCompanyModal();
        $this->company = Company::find($companyId);
    }

    public function addCompany(){
        $this->openCompanyModal();
        $this->company = new Company();
    }

    public function saveCompanyData(){

        if(!Auth::user()->hasRole(User::ROLE_SUPER_ADMIN))
            return;

        $this->validate();
        $this->company->save();
        $this->showToast('Saved','Company Saved','success');
        $this->closeCompanyModal();

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
        Company::find($this->deleteId)->delete();
        $this->closeSoftDeleteModal();
        $this->showToast('Company deleted','',);
    }
    //------------- Soft Delete End -----------------


    public function render()
    {
        $companies = Company::search('name',$this->search)->paginate(10);
        return view('livewire.company-overview',compact('companies'));
    }
}
