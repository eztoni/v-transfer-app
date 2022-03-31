<div>

    @if($this->showSearch)

        <x-ez-card class="mb-4">

            <x-slot name="title" class="flex justify-between">
                <span>Transfers <i class="fas fa-car-alt"></i></span>
            </x-slot>

            <x-slot name="body">

                <div class="form-control">

                    <label class="label">
                        <span class="label-text">Select a transfer :</span>
                    </label>
                    <select wire:model="transferId" class="select select-bordered">
                        <option value="">Select a transfer</option>
                        @foreach($transfers as $t)
                            <option value="{{$t->id}}">{{$t->name}}</option>
                        @endforeach
                    </select>

                    @error('transferId')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>

            </x-slot>
        </x-ez-card>

    @endif

    @if ($transferId)
        <x-ez-card>

            <x-slot name="title" class="flex justify-between">
                Transfer Prices
                <button wire:click="openPivotModal" class="btn btn-sm ">Add New Route</button>
            </x-slot>

            <x-slot name="body">

                @if ($this->getTransferRoutesProperty()->isNotEmpty())


                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <!-- head -->
                            <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Route</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($this->getTransferRoutesProperty() as $r)
                                <tr>
                                    <th>#{{$r->id}}</th>
                                    <td>{{$r->name}}</td>
                                    <td>
                                        <input wire:model="routePrice.{{$r->id}}" class="input input-bordered">
                                    </td>
                                    <td>
                                        <button wire:click="saveRoutePrice({{$r->id}})" class="btn btn-sm btn-success">
                                            Save
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>

                @else
                    No routes for this transfer, add new route!
                @endif


            </x-slot>

        </x-ez-card>
    @else
            <x-ez-card>
                <x-slot name="body">
                    Select a transfer to add prices!
                </x-slot>
            </x-ez-card>
    @endif


    <div class="modal {{ $pivotModal ? 'modal-open fadeIn' : '' }}">
        <div class="modal-box max-h-screen overflow-y-auto">
            Adding a new route
            <hr class="my-4">

            <div class="form-control">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Route :</span>
                    </label>
                    <select wire:model="routeId" class="select select-bordered">
                        <option value="">Select a transfer</option>
                        @foreach($this->getRoutesProperty() as $ro)
                            <option value="{{$ro->id}}">{{$ro->name}}</option>
                        @endforeach
                    </select>
                    @error('routeId')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>


                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Price :</span>
                    </label>
                    <input wire:model="price" class="input input-bordered"
                           placeholder="Price in EUR">
                    @error('price')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>


            </div>

            <div class="mt-4 flex justify-between">
                <button wire:click="closePivotModal()" class="btn btn-sm ">Close</button>
                <button wire:click="savePivotData()"
                        class="btn btn-sm ">Save</button>
            </div>
        </div>
    </div>


</div>

