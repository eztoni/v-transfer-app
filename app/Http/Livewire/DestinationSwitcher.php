<?php

namespace App\Http\Livewire;

use App\Models\Company;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DestinationSwitcher extends Component
{

    public function changeDestination($destinationId){
        if(!Auth::user()->hasAnyRole([User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN]))
            return;

        $user = Auth::user();
        $user->destination_id = $destinationId;
        $user->save();
        $this->redirect('/');
    }

    public function render()
    {
        $destinations = Destination::all();
        $userDestinationName = 'Destinations';
        if($destinations->isNotEmpty())
            $userDestinationName = $destinations->where('id','=',Auth::user()->destination_id)->first()->name;


        return view('livewire.destination-switcher',compact('destinations','userDestinationName'));
    }
}
