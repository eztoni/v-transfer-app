<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use WireUi\Traits\Actions;

class PartnerEdit extends Component
{
use Actions;

    public Partner $partner;
    public $selectedDestinations = [];
    public $partnerDestinations;

    public function mount(){

    }


    protected function rules()
    {
        return [
            'partner.name'=>'required|max:255|min:2',
            'selectedDestinations'=>'required',
            'partner.email'=>'required|email',
            'partner.phone'=>'required|max:255',
        ];
    }

    public function getDestinationPartnersProperty()
    {
        return Partner::with('destinations')->where('id', '=', $this->partner->id)->first()->destinations;
    }

    public function savePartnerData(){

        if(!Auth::user()->hasRole([User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN]))
            return;

        $this->partner->owner_id = Auth::user()->owner_id;
        $this->partner->destinations()->sync($this->selectedDestinations);
        $this->validate();
        $this->partner->save();
        $this->notification()->success('Saved','Partner Saved');

    }


    public function render()
    {
        $destinations = Destination::all();
        return view('livewire.partner-edit',compact('destinations'));
    }
}
