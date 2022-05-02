<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Livewire\Component;

class DestinationSwitcher extends Component
{

    public function changeOwner($destinationId){
        if(!Auth::user()->hasAnyRole([User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN]))
            return;

        $user = Auth::user();
        $user->destination_id = $destinationId;
        $user->save();

        $this->emit('destination_changed');
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
