<div>
    <x-ez-card>
        <x-slot name="title" class="flex justify-between">
            Vehicles

            <button wire:click="addVehicle" class="btn btn-sm ">Add Vehicle</button>

        </x-slot>
        <x-slot name="body">

            <input type="text" wire:model="search" class="input input-primary my-2" placeholder="Find Vehicle">
            <table class="table table-compact w-full" wire:loading.delay.class="opacity-50">
                <thead>
                <tr>
                    <th>#Id</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th class="text-center">Edit Vehicle</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($vehicles as $ve)

                    <tr>
                        <th>{{ $ve->id }}</th>
                        <th >{{ $ve->name }}</th>
                        <th >{{ $ve->type }}</th>

                        <td class="text-center">
                            <a href="{{ route('vehicle-edit',$ve) }}"><button class="btn btn-sm btn-success">Edit</button></a>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="999">
                            <div class="alert alert-warning">
                                <div class="flex-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         class="w-6 h-6 mx-2 stroke-current">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <label>No defined vehicles</label>
                                </div>
                            </div>
                        </TD>
                    </tr>
                @endforelse
                </tbody>

            </table>
            {{$vehicles->links()}}


            <div class="modal {{ $vehicleModal ? 'modal-open fadeIn' : '' }}">
                <div class="modal-box max-h-screen overflow-y-auto">
                    <b>{{  !empty($this->vehicle->exists) ? 'Updating':'Adding' }} vehicle</b>
                    <hr class="my-4">

                    <div class="form-control">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Name :</span>
                            </label>
                            <input wire:model="vehicleName" class="input input-bordered"
                                   placeholder="Name">
                            @error('vehicleName')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>

                    </div>

                    <div class="form-control">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Type : </span>
                            </label>
                            <input wire:model="vehicle.type" class="input input-bordered"
                                   placeholder="Vehicle Type">
                            @error('vehicle.type')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>
                    </div>

                    <div class="form-control">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Max Occ : </span>
                            </label>
                            <input wire:model="vehicle.max_occ" class="input input-bordered"
                                   placeholder="Max Occ.">
                            @error('vehicle.max_occ')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>
                    </div>

                    <div class="form-control">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Max Luggage : </span>
                            </label>
                            <input wire:model="vehicle.max_luggage" class="input input-bordered"
                                   placeholder="Max Luggage">
                            @error('vehicle.max_luggage')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 flex justify-between">
                        <button wire:click="closeVehicleModal()" class="btn btn-sm ">Close</button>
                        <button wire:click="saveVehicleData()"
                                class="btn btn-sm ">{{  !empty($this->vehicle->exists) ? 'Update':'Add' }}</button>
                    </div>
                </div>
            </div>

        </x-slot>

    </x-ez-card>

</div>

