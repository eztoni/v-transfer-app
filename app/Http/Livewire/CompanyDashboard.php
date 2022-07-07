<?php

namespace App\Http\Livewire;

use App\Models\Company;
use App\Models\Language;
use App\Models\User;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CompanyDashboard extends Component
{
    use WithFileUploads;

    public $photo;
    public $company;
    public $companyModal = false;
    public $selectedLanguages = [1];
    public $imageUrl = false;

    public function mount()
    {
        $company_id = Auth::user()->company_id;
        $this->company = Company::findOrFail($company_id);
        $this->selectedLanguages = $this->company->languages->pluck('id')->toArray();

        $this->setImageUrl();
    }

    private function setImageUrl()
    {
        $this->imageUrl = $this->company->getFirstMediaUrl('logo', 'thumb');
        if (empty($this->imageUrl)) {
            $this->imageUrl = config('ez.default_img_url');
        }
    }

    protected $rules = [
        'company.name' => 'required|max:255',
        'company.zip' => 'required|max:255',
        'company.country_id' => 'required',
        'company.city' => 'required|max:255',
        'company.contact' => 'required|max:255',
        'company.email' => 'required|email|max:255',
    ];

    public function toggleCompanyData()
    {
        $this->companyModal = true;
    }


    public function getCompanyLanguagesProperty()
    {
        return Company::with('languages')->where('id', '=', $this->company->id)->first()->languages;
    }

    public function saveCompanyData()
    {
        if (!Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN, User::ROLE_SUPER_ADMIN))
            return;

        $this->validate();
        $this->company->languages()->sync($this->selectedLanguages);
        $this->company->save();
        $this->showToast('Saved', 'Company Saved', 'success');
    }

    public function savePhoto()
    {
        $this->validate([
            'photo' => 'mimes:jpg,png|max:2024', // 1MB Max
        ]);
        $path = $this->photo->store('tempImages');
        $this->company->addMedia(Storage::path($path))->toMediaCollection('logo');
        $this->setImageUrl();
        $this->showToast('Saved', 'Image saved', 'success');

    }

    public function render()
    {
        $usersCount = User::whereHas('roles', function ($query) {
            $query->where('name', '!=', User::ROLE_SUPER_ADMIN);
        })->where('company_id', '=', $this->company->id)
            ->count();

        $languages = Language::withoutGlobalScope(CompanyScope::class)->get();
        return view('livewire.company-dashboard', compact('languages', 'usersCount'));
    }
}
