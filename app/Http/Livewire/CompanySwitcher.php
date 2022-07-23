<?php

namespace App\Http\Livewire;

use App\Models\Company;
use App\Models\Destination;
use App\Models\Owner;
use App\Models\User;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use WireUi\Traits\Actions;

class CompanySwitcher extends Component
{
use Actions;

    public function changeCompany($companyId){
        if(!Auth::user()->hasRole(User::ROLE_SUPER_ADMIN))
            return;

        $firstDestination = Destination::withoutGlobalScope(CompanyScope::class)->where('company_id','=',$companyId)->first()->id;

        if(!$firstDestination)
            return;

        $firstOwner = Owner::withoutGlobalScope(CompanyScope::class)->where('company_id','=',$firstDestination->id)->first()->id;
        if(!$firstOwner)
            return;

        $user = Auth::user();
        $user->company_id = $companyId;
        $user->owner_id = $firstOwner;
        $user->destination_id = $firstDestination;
        $user->save();
        $this->redirect('/');
    }

    public function render()
    {
        $companies = Company::all();
        $userCompanyName = 'Companies';
        if($companies->isNotEmpty())
            $userCompanyName = $companies->where('id','=',Auth::user()->company_id)->first()->name;

        return view('livewire.company-switcher',compact('companies','userCompanyName'));
    }
}
