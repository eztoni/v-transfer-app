<x-card title="User Overview">
    <x-slot name="action" >
        <x-button wire:click="addUser" positive>Add User</x-button>
    </x-slot>
    <table class="ds-table ds-table-compact w-full" wire:loading.delay.class="opacity-50">
        <thead>
        <tr>
            <th>#ID</th>
            <th>Name</th>
            <th>E-mail</th>
            <th>Role</th>
            <th class="text-center">Edit</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($users as $user_data)
            @if($currentUser->id != $user->id)
                <tr>
                    <th>{{ $user_data->id }}</th>
                    <td>{{ $user_data->name }}</td>
                    <td>{{ $user_data->email }}</td>
                    <td>@foreach($user_data->getRoleNames() as $roleName) <span class="badge"> {{$roleName}}</span> @endforeach</td>
                    @if($user_data->hasRole('super-admin'))
                        <td class="text-center">
                            <x-button.circle disabled primary icon="pencil">
                            </x-button.circle>
                        </td>
                    @else
                        <td class="text-center">
                            <x-button.circle primary wire:click="updateUser({{$user_data->id}})" icon="pencil">
                            </x-button.circle>
                        </td>
                   @endif
                </tr>
            @endif
        @empty
            <tr>
                <td colspan="999">
                    <div class="ds-alert ds-alert-warning">
                        <div class="flex-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 class="w-6 h-6 mx-2 stroke-current">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <label>No defined users</label>
                        </div>
                    </div>
                </TD>
            </tr>
        @endforelse
        </tbody>
    </table>


    <x-modal.card wire:model="userModal" title="{{  !empty($this->user->exists) ? 'Updating':'Adding' }} user">
        <div class="">

            <x-input label="Name:" wire:model="user.name"/>

            <x-native-select
                placeholder="Select an owner"
                wire:model="user.owner_id"
                label="Owner:"
                option-key-value
                :options="$owners"
            />

            <x-input label="Email:" wire:model="user.email"/>
            <x-input label="OIB:" wire:model="user.oib"/>
{{--            <x-input label="City:" wire:model="user.city"/>--}}
{{--            <x-input label="Zip:" wire:model="user.zip"/>--}}

            @if($this->user)
                <x-select
                    label="Available Destinations:"
                    placeholder="Select destinations"
                    multiselect
                    option-label="name"
                    option-value="id"
                    :options="$destinations->map(fn ($m) => ['id'=>$m->id,'name'=>$m->name])->toArray()"
                    wire:model.defer="selectedDestinations"
                >
                </x-select>
            @endif

            @if($this->user->id > 0)
                <x-input label="Password:" wire:model="user.set_password"/>
                <x-input label="Password Confirmation:" wire:model="user.set_password_confirmation"/>
            @endif

            <x-native-select
                placeholder="Select a role"
                wire:model="userRole"
                label="Role:"
                :options="$roles->mapWithKeys(function ($i) {
            return [$i->id => $i->name];
        })->toArray()"
            />
@error('*')
            {{$message}}
            @enderror
            <x-slot name="footer">
                <div class="mt-4 flex justify-between">
                    <x-button wire:click="closeUserModal()" >Close</x-button>
                    <x-button wire:click="saveUserData()" positive
                    >{{  !empty($this->user->exists) ? 'Update':'Add' }}</x-button>
                </div>
            </x-slot>
        </div>

    </x-modal.card>



</x-card>

