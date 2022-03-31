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

                @if ($this->routes->isNotEmpty())


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
                            @foreach ($this->routes as $r)
                                <tr x-data="{dirty:true}">
                                    <th>#{{$r->id}}</th>
                                    <td>{{$r->name}}</td>
                                    <td>
                                        <input wire:model="routePrice.{{$r->id}}" @keyup="dirty=false" class="input input-bordered">
                                        @error('routePrice.'.$r->id)
                                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                        @enderror
                                    </td>
                                    <td>
                                        <button wire:click="saveRoutePrice({{$r->id}})"  :disabled="dirty"  class="btn  btn-sm btn-success">
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




</div>

