<?php

namespace App\Http\Livewire;

use App\Models\Company;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OwnerSwitcher extends Component
{

    public function changeOwner($ownerId){
        if(!Auth::user()->hasAnyRole([User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN]))
            return;

        $owner = Owner::findOrFail($ownerId);

        if($owner->destinations->isEmpty()){
            $this->showToast('Action failed!','Selected owner has no destinations!','error');
            return;
        }

        $user = Auth::user();
        $user->owner_id = $ownerId;
        $user->destination_id = $owner->destinations->first()->id;

        $user->save();
        $this->redirect('/');
    }

    public function render()
    {
        $owners = Owner::all();
        $userOwnerName = 'Owners';
        if($owners->isNotEmpty())
            $userOwnerName = $owners->where('id','=',Auth::user()->owner_id)->first()->name;



        return view('livewire.owner-switcher',compact('owners','userOwnerName'));
    }
}
