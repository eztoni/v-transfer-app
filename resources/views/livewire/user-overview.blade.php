<div >
    <x-ez-card>
        <x-slot name="title">

            <div class="flex justify-between">
                <h2>Users</h2>
                <button wire:click="addUser" class="btn btn-sm ">Add User</button>
            </div>
        </x-slot>
        <x-slot name="body">
            <table class="ds-table ds-table-compact w-full">
                <thead>
                <tr>
                    <th>#ID</th>
                    <th>Name</th>
                    <th>E-mail</th>
                    <th>Role</th>
                    <th class="text-right">Edit</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($users as $user_data)
                    @if($currentUser->id != $user->id)
                    <tr>
                        <th>{{ $user_data->id }}</th>
                        <td>{{ $user_data->name }}</td>
                        <td>{{ $user_data->email }}</td>
                        <td >@foreach($user_data->getRoleNames() as $roleName) <span class="badge"> {{$roleName}}</span> @endforeach</td>
                        <td class="text-right">
                            @if($user_data->hasRole('super-admin'))
                                <button disabled class="btn btn-primary btn-sm disabled">Update</button>
                            @else
                                <button wire:click="updateUser({{$user_data->id}})" class="btn btn-sm btn-success">
                                    Update
                                </button>
                            @endif
                        </td>

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
                                    <label>No active users</label>
                                </div>
                            </div>
                        </TD>
                    </tr>
                @endforelse
                </tbody>

            </table>

            <div x-data="{}" class="modal {{ $userModal ? 'modal-open fadeIn' : '' }}">
                <div class="modal-box max-h-screen overflow-y-auto">
                    {{ !empty($this->user->exists) ? 'Updating':'Adding' }} User
                    <hr class="my-4">

                    <div class="form-control">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Name :</span>
                            </label>
                            <input wire:model="user.name" class="input input-bordered"
                                   placeholder="Name">
                            @error('user.name')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Owner:</span>
                            </label>

                            <select class="select select-bordered" wire:model="user.owner_id">
                                <option  value="{{ null }}">Select Owner</option>
                                @foreach($owners as $owner)
                                    <option value="{{$owner->id}}">{{$owner->name}}</option>
                                @endforeach
                            </select>
                            @error('user.owner_id') <x-input-alert type='warning'>{{ $message }}</x-input-alert>@enderror
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Email:</span>
                            </label>
                            <input wire:model="user.email" class="input input-bordered"
                                   placeholder="Email">
                            @error('user.email')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">OIB:</span>
                            </label>
                            <input wire:model="user.oib" class="input input-bordered"
                                   placeholder="OIB">
                            @error('user.oib')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">City:</span>
                            </label>
                            <input wire:model="user.city" class="input input-bordered"
                                   placeholder="City">
                            @error('user.city')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Zip:</span>
                            </label>
                            <input wire:model="user.zip" class="input input-bordered"
                                   placeholder="Zip">
                            @error('user.zip')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>

                        @if($this->user)
                            <div class="form-control" wire:ignore>

                                <label class="label">
                                    <span class="label-text">Available Destinations:</span>
                                </label>
                                <select class="input input-bordered" multiple id="select2">

                                    @foreach($destinations as $dest)
                                        <option value="{{$dest->id}}"> {{ Str::ucfirst($dest->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @error('selectedDestinations')
                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                        @enderror

                        @if(!$this->user->exists)
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Password:</span>
                                </label>
                                <input autocomplete="off"  wire:model="user.set_password" class="input input-bordered" placeholder="Password" type="password">
                                @error('user.set_password') <x-input-alert type='warning'>{{ $message }}</x-input-alert>@enderror
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Password Confirmation:</span>
                                </label>
                                <input  autocomplete="off" wire:model="user.set_password_confirmation" class="input input-bordered" placeholder="Password Confirmation" type="password">
                                @error('user.set_password_confirmation') <x-input-alert type='warning'>{{ $message }}</x-input-alert>@enderror
                            </div>
                        @endif

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Role:</span>
                            </label>

                            <select class="select select-bordered" wire:model="userRole">
                                <option  value="{{ null }}">Select Role</option>
                                @foreach($roles as $role)
                                    <option  value="{{$role->name}}">{{\Illuminate\Support\Str::headline($role->name)}}</option>
                                @endforeach
                            </select>
                            @error('userRole') <x-input-alert type='warning'>{{ $message }}</x-input-alert>@enderror
                        </div>




                    </div>

                    <div class="mt-4 flex justify-between">
                        <button wire:click="closeUserModal()" class="btn btn-sm ">Close</button>
                        <button wire:click="saveUserData()"
                                class="btn btn-sm ">{{  !empty($this->user->exists) ? 'Update':'Add' }}</button>
                    </div>
                </div>
            </div>

        </x-slot>
    </x-ez-card>

    <script>
        document.addEventListener('livewire:load', function () {
            // Run a callback when an event ("foo") is emitted from this component
            @this.on('fillSelect2', () => {

                $("#select2").val('')
                $('#select2').select2(
                    {
                        closeOnSelect: true,
                    }
                ).on('change', function (e) {
                    @this.set('selectedDestinations', $('#select2').select2("val"))
                })


                for (const element of @this.userDestinations) {
                    $("#select2").select2("trigger", "select", {
                        data: { id: element }
                    });
                }

            })


            @this.on('restartSelect2', () => {
                $("#select2").val('')
                $('#select2').select2(
                    {
                        closeOnSelect: true,
                    }
                ).on('change', function (e) {
                    @this.set('selectedDestinations', $('#select2').select2("val"))
                })
            })
        });
    </script>

</div>
