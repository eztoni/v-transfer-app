<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Livewire\Component;
use WireUi\Traits\Actions;

class DestinationSwitcher extends Component
{
use Actions;

    public function changeDestination($destinationId){
        if(!Auth::user()->hasAnyRole([User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN,User::ROLE_USER,User::ROLE_RECEPTION,User::ROLE_REPORTAGENT]))
            return;

        $user = Auth::user();
        $user->destination_id = $destinationId;
        $user->save();

        $this->emit('destination_changed');
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
