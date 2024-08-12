<?php

namespace App\Http\Livewire;

use App\Models\Destination;
use App\Models\Owner;
use App\Models\User;
use App\Scopes\OwnerScope;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use WireUi\Traits\Actions;
use function PHPUnit\Framework\isEmpty;


class UserOverview extends Component
{
use Actions;

    public $user;
    public $userModal;
    public $userRole = '';
    public $selectedDestinations = [];

    protected function rules()
    {
        return [
            'user.name' => 'required|min:3',
            'user.email' => 'required|email|unique:users,email,'.$this->user->id,
            'user.owner_id' => 'required',
            'user.city' => '',
            'user.zip' => '',
            'user.oib' => 'digits:11|integer|unique:users,oib,'.$this->user->id,
            'user.set_password'=>'nullable|min:6',
            'user.set_password_confirmation'=>'nullable|same:user.set_password',
            'userRole'=>'required|in:admin,user,reception',
            'selectedDestinations' => 'required'
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
        $this->selectedDestinations = [];
    }

    public function updateUser($userId){

        $this->openUserModal();
        $this->user = User::findOrFail($userId);
        $this->userRole = $this->user->getRoleNames()->first();
        $this->selectedDestinations = $this->user->availableDestinations->pluck('id')->toArray();

    }

    public function fillSelect2()
    {
        $this->emit('fillSelect2');
    }

    public function updatedUserOwnerId(){
        $this->selectedDestinations = [];
    }

    public function saveUserData(){

        $this->validate();

        if(!Auth::user()->hasAnyRole(User::ROLE_SUPER_ADMIN,User::ROLE_ADMIN)) {
            return;
        }

        if($this->user->exists && $this->user->company_id != Auth::user()->company_id ) {
            return;
        }



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
        $this->user->destination_id = $this->selectedDestinations[0];
        $this->user->save();
        $this->user->assignRole($this->userRole);
        $this->user->availableDestinations()->sync($this->selectedDestinations);
        $this->user->forgetCachedPermissions();

        $this->notification()->success('User Saved','');
        $this->closeUserModal();

    }


    public function render()
    {

        $currentUser = Auth::user();
        $users = User::with('roles')
            ->where('company_id','=',Auth::user()->company_id)
            ->whereDoesntHave('roles',function ($q){
                $q->where('name','super-admin');
            })
            ->get();

        // One more check :)
        $users = $users->reject(function ($user, $key) {
            return $user->hasRole(User::ROLE_SUPER_ADMIN);
        });


        $roles = \Spatie\Permission\Models\Role::where('name','!=','super-admin')->get();

        $owners = Owner::all()->mapWithKeys(function ($i) {
            return [$i->id => $i->name];
        })->toArray();

        $destinations = Destination::withoutGlobalScope(OwnerScope::class)->where('owner_id',$this->user->owner_id)->get();

        return view('livewire.user-overview',compact('users','currentUser','roles','owners','destinations'));
    }

    public function translateUserRole($userRole){

        $return = $userRole;

        switch ($userRole){
            case User::ROLE_USER:
                $return = 'VRC';
                break;
            case User::ROLE_ADMIN:
                $return = 'Administrator';
                break;
            case User::ROLE_RECEPTION:
                $return = 'Reception';
                break;
            case User::ROLE_REPORTAGENT:
                $return = 'Report Agent';
                break;
        }
        return $return;
    }
}
