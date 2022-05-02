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
                                <th>Two Way</th>
                                <th>Two Way Price (EUR)</th>
                                <th class="text-right pr-8">Price (EUR)</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($this->routes as $r)
                                <tr x-data="{dirty:{{'true'}} }"  wire:key="{{$r->id}}">
                                    <th>#{{$r->id}}</th>
                                    <td>{{$r->name}}</td>
                                    <td>{{$r->startingPoint->name}}</td>
                                    <td>{{$r->endingPoint->name}}</td>
                                    <td><input wire:click="saveRoundTrip({{$r->id}})" wire:model="routeRoundTrip.{{$r->id}}" @if(!empty($routeRoundTrip[$r->id])) checked @endif type="checkbox" class="toggle toggle-primary"></td>
                                    <td class="text-right">
                                        <div class="form-control">
                                            <div class="input-group justify">
                                                <input wire:model="routePriceRoundTrip.{{$r->id}}" @if(empty($routeRoundTrip[$r->id])) disabled @endif placeholder="Price" class=" @error('routePriceRoundTrip.'.$r->id) input-error @enderror input input-sm input-bordered">
                                                <button wire:click="saveRoutePriceRoundTrip({{$r->id}})" @if(empty($routeRoundTrip[$r->id])) disabled @endif class="btn btn-sm  btn-success">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="form-control ">
                                            <div class="input-group justify-end">
                                                <input wire:model="routePrice.{{$r->id}}" placeholder="Price" @keyup="dirty=false" class="@error('routePrice.'.$r->id) input-error @enderror input input-sm input-bordered">
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

