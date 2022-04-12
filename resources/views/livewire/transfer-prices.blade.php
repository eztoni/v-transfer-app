<div>

    @if($this->showSearch)

        <x-ez-card class="mb-4">

            <x-slot name="title" class="flex justify-between">
                <span>Transfers <i class="fas fa-car-alt"></i></span>
            </x-slot>

            <x-slot name="body">

                <div class="form-control">

                    <label class="label">
                        <span class="label-text">Select a transfer:</span>
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
              <div class="">
                  Transfer Prices
              </div>
                <div class="">
                    <label class="label">
                        <span class="label-text">Partner:</span>
                    </label>
                    <select class="my-select" wire:model="partnerId">
                        <option value="0">{{\Auth::user()->owner->name}}</option>
                        @foreach(\App\Models\Partner::all() as $partner)
                            <option value="{{$partner->id}}">{{$partner->name}}</option>
                        @endforeach
                    </select>
                </div>
            </x-slot>

            <x-slot name="body">

                @if ($this->routes->isNotEmpty())


                        <table class="table table-compact">
                            <!-- head -->
                            <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Route name</th>
                                <th>From</th>
                                <th>To</th>

                                <th class="text-right pr-8">Price</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($this->routes as $r)
                                <tr x-data="{dirty:{{'true'}} }"  wire:key="{{$r->id}}">
                                    <th>#{{$r->id}}</th>
                                    <td>{{$r->name}}</td>
                                    <td>{{$r->startingPoint->name}}</td>
                                    <td>{{$r->endingPoint->name}}</td>
                                    <td class="text-right">
                                        <div class="form-control ">
                                            <div class="input-group justify-end">
                                                <input wire:model="routePrice.{{$r->id}}" placeholder="Price" @keyup="dirty=false" class="input input-sm input-bordered">
                                                @error('routePrice.'.$r->id)
                                                <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                                @enderror
                                                <button wire:click="saveRoutePrice({{$r->id}})"  :disabled="dirty"  class="btn btn-sm  btn-success">
                                                    Save
                                                </button>
                                            </div>
                                        </div>

                                    </td>

                                </tr>
                            @endforeach

                            </tbody>
                        </table>

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

