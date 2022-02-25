<div>

    <x-ez-card>
        <x-slot name="title" class="flex justify-between">
            Points

            <button wire:click="addPoint" class="btn btn-sm ">Add Point</button>

        </x-slot>
        <x-slot name="body">

            <input type="text" wire:model="search" class="input input-primary my-2" placeholder="Find Point">
            <table class="table table-compact w-full" wire:loading.delay.class="opacity-50">
                <thead>
                <tr>
                    <th>#Id</th>
                    <th>Name</th>
                    <th class="text-center">Update</th>
                    <th class="text-right"><span class="pr-4">Delete</span></th>

                </tr>
                </thead>
                <tbody>
                @forelse ($points  as $p)

                    <tr>
                        <th>{{ $p->id }}</th>
                        <th>{{ $p->name }}</th>
                        <td class="text-center">
                            <button wire:click="updatePoint({{$p->id}})" class="btn btn-sm btn-success">
                                Update
                            </button>
                        </td>
                        <td class="text-right">
                            <button wire:click="openSoftDeleteModal({{$p->id}})" class="btn btn-sm btn-ghost">
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
                                    <label>No defined points</label>
                                </div>
                            </div>
                        </TD>
                    </tr>
                @endforelse
                </tbody>

            </table>
            {{$points->links()}}

            <div class="modal {{ $softDeleteModal ? 'modal-open fadeIn' : '' }}">
                <div class="modal-box max-h-screen overflow-y-auto">
                    <b>Confirm deletion?</b>
                    <p>This action will delete the point.</p>
                    <hr class="my-4">

                    <div class="mt-4 flex justify-between">
                        <button wire:click="closeSoftDeleteModal()" class="btn btn-sm ">Close</button>
                        <button wire:click="softDelete()" class="btn btn-sm ">Delete</button>
                    </div>
                </div>
            </div>


            <div class="modal {{ $pointModal ? 'modal-open fadeIn' : '' }}">
                <div class="modal-box max-h-[45rem] overflow-y-auto">
                    Adding new point
                    <hr class="my-4">

                    <div class="form-control">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Name :</span>
                            </label>
                            <input wire:model="point.name" class="input input-bordered"
                                   placeholder="Name">
                            @error('point.name')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>

                        <div class="form-control">

                            <label class="label">
                                <span class="label-text">Destination:</span>
                            </label>
                            <select wire:model="point.destination_id" class="select select-bordered">
                                <option value="">Select a destination</option>
                                @foreach($destinations as $destination)
                                    <option value="{{$destination->id}}">{{$destination->name}}</option>
                                @endforeach
                            </select>

                            @error('point.destination_id')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Description:</span>
                            </label>
                            <textarea rows="2" wire:model="point.description" class="textarea textarea-bordered"
                                      placeholder="ex. Near pile gate"></textarea>
                            @error('point.description')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>
                        <div class="form-control" >
                            <label for="address_address">Address</label>
                            <input type="text" autocomplete="off" id="address-input" value="{{$point->address}}" class="input input-bordered rounded-b-none map-input">
                            <input type="hidden"  wire:model="point.address" id="address-address" value="{{$point->address}}" />
                            <input type="hidden"  wire:model="point.latitude" id="address-latitude" value="{{$point->latitude}}" />
                            <input type="hidden"  wire:model="point.longitude" id="address-longitude" value="{{$point->longitude}}" />


                        </div>
                        <div id="address-map-container" wire:ignore class="pt-2" style="width:100%;height:400px; ">
                            <div style="width: 100%; height: 100%" id="address-map"></div>
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Type:</span>
                            </label>
                            <select wire:model="point.type" class="select select-bordered">
                                <option value="">Select a point type</option>
                                @foreach(\App\Models\Point::TYPE_ARRAY as $type)
                                    <option value="{{$type}}">{{Str::headline($type)}}</option>
                                @endforeach
                            </select>

                            @error('point.destination_id')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">HIS code:</span>
                            </label>
                            <input wire:model="point.his_code" class="input input-bordered"
                            >
                            @error('point.his_code')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>


                    </div>

                    <div class="mt-4 flex justify-between">
                        <button wire:click="closePointModal()" class="btn btn-sm ">Close</button>
                        <button wire:click="savePointData()"
                                class="btn btn-sm ">{{  !empty($this->point->exists) ? 'Update':'Add' }}</button>
                    </div>
                </div>
            </div>

        </x-slot>

    </x-ez-card>
    @push('scripts-bottom')
        @once
            <script
                src="https://maps.googleapis.com/maps/api/js?key={{ Config::get('valamar.google_maps_api_key') }}&libraries=places&callback=initialize"
                async defer></script>
            <script src="{{mix('js/mapInput.js')}}"></script>
        @endonce
    @endpush
</div>

