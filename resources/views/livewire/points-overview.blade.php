<div>



    @if ($destinationId)
        <x-ez-card>

            <x-slot name="title" class="flex justify-between">
                {{$destination->name}} - Pickup & Dropoff Points

                <button wire:click="addPoint" class="btn btn-sm ">Add Point</button>
            </x-slot>

            <x-slot name="body">

                @if ($this->points->isNotEmpty())


                    <table class="table table-compact">
                        <!-- head -->
                        <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Name</th>
                            <th>Update</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($this->points as $p)
                            <tr>
                                <td>{{ $p->id }}</td>
                                <td>{{ $p->name }}</td>
                                <td>
                                    <button wire:click="updatePoint({{$p->id}})" class="btn btn-circle btn-sm btn-success">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                @else
                    No Pickup&Dropoff Points for {{$destination->name}}, add new points!
                @endif


            </x-slot>

        </x-ez-card>
    @else
        <x-ez-card>
            <x-slot name="body">
                Select a destination to add Pickup&Dropoff Points!
            </x-slot>
        </x-ez-card>
    @endif

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
                        <span class="label-text">Description:</span>
                    </label>
                    <textarea rows="2" wire:model="point.description" class="textarea textarea-bordered"
                              placeholder="ex. Near pile gate"></textarea>
                    @error('point.description')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>
                <div class="form-control" >
                    <label>Address</label>
                    <input wire:model="point.address" class="input input-bordered"
                           placeholder="Address">
                    @error('point.address')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
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

                    @error('point.point.type')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">PMS code:</span>
                    </label>
                    <input wire:model="point.pms_code" class="input input-bordered"
                    >
                    @error('point.pms_code')
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

</div>

