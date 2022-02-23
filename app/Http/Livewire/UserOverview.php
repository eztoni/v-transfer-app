<?php

namespace App\Http\Livewire;

use App\Models\Owner;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserOverview extends Component
{

    public $user;
    public $userModal;
    public $userRole = '';
    public $editData = [];


    protected function rules()
    {
        return [
            'user.name' => 'required|min:3',
            'user.email' => 'required|email',
            'user.owner_id' => 'required',
            'user.city' => 'min:3',
            'user.zip' => 'min:3',
            'user.oib' => 'digits:13|integer|unique:users,oib,'.$this->user->id,
            'user.set_password'=>'nullable|min:6',
            'user.set_password_confirmation'=>'nullable|same:user.set_password',
            'userRole'=>'required|in:admin,user'
        ];
    }

    public function mount()
    {
        $this->user = new User();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function openUserModal(){
        $this->userModal = true;
    }

    public function closeUserModal(){
        $this->userModal = false;
        $this->userRole = '';
    }

    public function addUser(){
        $this->openUserModal();
        $this->user = new User();
    }

    public function updateUser($userId){

        $this->openUserModal();
        $this->user = User::find($userId);
        $this->userRole = $this->user->getRoleNames()->first();

    }

    public function saveUserData(){
        $this->validate();

        if(!Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN))
            return;

        if($this->user->exists && $this->user->company_id != Auth::user()->company_id )
            return;



        if($this->user->id){
            if($this->user->roles->first())
                $this->user->removeRole($this->user->roles->first());
            unset($this->user->set_password_confirmation);
            unset($this->user->set_password);
        }
        else{
            $this->user->password=\Hash::make($this->user->set_password);
            unset($this->user->set_password_confirmation);
            unset($this->user->set_password);
        }

        $this->user->company_id = Auth::user()->company_id;
        $this->user->save();
        $this->user->assignRole($this->userRole);
        $this->user->forgetCachedPermissions();

        $this->showToast('User Saved','','success');
        $this->closeUserModal();

    }


    public function render()
    {
        $currentUser = Auth::user();
        $users = User::with('roles')->where('company_id','=',Auth::user()->company_id)->get();
        $users = $users->reject(function ($user, $key) {
            return $user->hasRole(User::ROLE_SUPER_ADMIN);
        });
        $roles = \Spatie\Permission\Models\Role::where('name','!=','super-admin')->get();
        $owners = Owner::all();

        return view('livewire.user-overview',compact('users','currentUser','roles','owners'));
    }
}
