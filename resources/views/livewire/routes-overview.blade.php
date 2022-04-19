<div>

    <x-ez-card>
        <x-slot name="title" class="flex justify-between">
            Routes

            <button wire:click="addRoute" class="btn btn-sm ">Add Route</button>

        </x-slot>
        <x-slot name="body">

            <input type="text" wire:model="search" class="input input-primary my-2" placeholder="Find Route">
            <table class="table table-compact w-full" wire:loading.delay.class="opacity-50">
                <thead>
                <tr>
                    <th>#Id</th>
                    <th>Name</th>
                    <th>Start</th>
                    <th>End</th>
                    <th class="text-center">Update</th>
                    <th class="text-right"><span class="pr-4">Delete</span></th>

                </tr>
                </thead>
                <tbody>
                @forelse ($routes as $r)

                    <tr>
                        <th>{{ $r->id }}</th>
                        <th >{{ $r->name }}</th>
                        <th >{{$r->startingPoint->name}}</th>
                        <th >{{ $r->endingPoint->name }}</th>
                        <td class="text-center">
                            <button wire:click="updateRoute({{$r->id}})" class="btn btn-sm btn-success">
                                Update
                            </button>
                        </td>
                        <td class="text-right">
                            <button wire:click="openSoftDeleteModal({{$r->id}})" class="btn btn-sm btn-ghost">
                                Delete
                            </button>
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
                                    <label>No defined routes</label>
                                </div>
                            </div>
                        </TD>
                    </tr>
                @endforelse
                </tbody>


            </table>
            {{$routes->links()}}


            <div class="modal {{ $softDeleteModal ? 'modal-open fadeIn' : '' }}">
                <div class="modal-box max-h-screen overflow-y-auto">
                    <b>Confirm deletion?</b>
                    <p>This action will delete the company.</p>
                    <hr class="my-4">

                    <div class="mt-4 flex justify-between">
                        <button wire:click="closeSoftDeleteModal()" class="btn btn-sm ">Close</button>
                        <button wire:click="softDelete()" class="btn btn-sm ">Delete</button>
                    </div>
                </div>
            </div>


            <div class="modal {{ $routeModal ? 'modal-open fadeIn' : '' }}">
                <div class="modal-box max-h-screen overflow-y-auto">
                    Adding new route
                    <hr class="my-4">

                    <div class="form-control">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Name :</span>
                            </label>
                            <input wire:model="route.name" class="input input-bordered"
                                   placeholder="Name">
                            @error('route.name')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>
                    </div>

                    <div class="form-control">

                        <label class="label">
                            <span class="label-text">Destination:</span>
                        </label>
                        <select wire:model="route.destination_id" class="select select-bordered">
                            <option value="">Select a destination</option>
                            @foreach($destinations as $destination)
                                <option value="{{$destination->id}}">{{$destination->name}}</option>
                            @endforeach
                        </select>

                        @error('route.destination_id')
                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                        @enderror
                    </div>


                    <div class="form-control">

                        <label class="label">
                            <span class="label-text">Starting point:</span>
                        </label>
                        <select wire:model="route.starting_point_id" class="select select-bordered">
                            <option value="">Select a starting point</option>
                            @foreach($this->startingPoints as $starting_points)
                                <option value="{{$starting_points['id']}}">{{$starting_points['name']}}</option>
                            @endforeach
                        </select>

                        @error('route.starting_point_id')
                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                        @enderror
                    </div>

                    <div class="form-control">

                        <label class="label">
                            <span class="label-text">Ending point:</span>
                        </label>
                        <select wire:model="route.ending_point_id" class="select select-bordered">
                            <option value="">Select a ending point</option>
                            @foreach($this->endingPoints as $ending_points)
                                <option value="{{$ending_points['id']}}">{{$ending_points['name']}}</option>
                            @endforeach
                        </select>

                        @error('route.ending_point_id')
                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                        @enderror
                    </div>

                    <div class="form-control">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">PMS code :</span>
                            </label>
                            <input wire:model="route.pms_code" class="input input-bordered"
                                   placeholder="PMS code">
                            @error('route.pms_code')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>
                    </div>



                    <div class="mt-4 flex justify-between">
                        <button wire:click="closeRouteModal()" class="btn btn-sm ">Close</button>
                        <button wire:click="saveRouteData()"
                                class="btn btn-sm ">{{  !empty($this->route->exists) ? 'Update':'Add' }}</button>
                    </div>
                </div>
            </div>

        </x-slot>

    </x-ez-card>
</div>

