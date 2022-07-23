<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use WireUi\Traits\Actions;

class EditUserDataBySuperadmin extends Component
{
use Actions;

    public $user;
    public $editData =[];

    protected $rules = [
        'editData.name' => 'required|min:6',
        'editData.email' => 'required|email',
        'editData.city' => 'required',
        'editData.zip' => 'required',
        'editData.country_code' => 'required|max:2',
    ];


    public function mount(){


        $this->editData = $this->user->only([
            'name',
            'email',
            'city',
            'zip',
            'country_code',
        ]);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function saveUserData(){
        $this->validate();
        $this->user->fill($this->editData);
        $this->user->save();
        $this->notification()->success('User Saved','');

    }

    public function render()
    {
        return view('livewire.edit-user-data-by-superadmin');
    }
}
