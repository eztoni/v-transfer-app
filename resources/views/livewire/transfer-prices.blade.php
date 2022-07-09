<x-card title="Transfer Prices">

    <x-slot name="action" >

        @if($this->showSearch)
            <x-native-select
                label="Transfers:"
                placeholder="Select a transfer"
                option-label="name"
                option-value="id"
                :options="$transfers->map(fn ($m) => ['id'=>$m->id,'name'=>$m->name])->toArray() "
                wire:model="transferId"
            />
        @endif

        @if ($transferId)
                <x-native-select
                    label="Partner:"
                    option-label="name"
                    option-value="id"
                    :options="\App\Models\Partner::all()->map(fn ($m) => ['id'=>$m->id,'name'=>$m->name])->toArray() "
                    wire:model="partnerId"
                />
        @endif


    </x-slot>

    <table class="ds-table ds-table-compact w-full" wire:loading.delay.class="opacity-50">
        <thead>
        <tr>
            <th>#ID</th>
            <th>Route name</th>
            <th>From</th>
            <th>To</th>
            <th>Round Trip</th>
            <th>Round Trip Price (EUR)</th>
            <th class="text-right pr-8">Price (EUR)</th>
        </tr>
        </thead>
        <tbody>


        @if ($transferId)

            @if ($this->routes->isNotEmpty())
                @forelse ($this->routes as $r)

                    <tr x-data="{dirty:{{'true'}} }"  wire:key="{{$r->id}}">
                        <th>#{{$r->id}}</th>
                        <td>{{$r->name}}</td>
                        <td>{{$r->startingPoint->name}}</td>
                        <td>{{$r->endingPoint->name}}</td>
                        <td>
                            <x-toggle wire:click="saveRoundTrip({{$r->id}})" wire:model="routeRoundTrip.{{$r->id}}" />
                        </td>
                        <td>
                            <div class="form-control">
                                <div class="input-group justify">

                                    <input wire:model="routePriceRoundTrip.{{$r->id}}" @if(empty($routeRoundTrip[$r->id])) disabled @endif placeholder="Price" class=" @error('routePriceRoundTrip.'.$r->id) ds-input-error @enderror ds-input ds-input-sm ds-input-bordered">

                                    @if(!empty($routeRoundTrip[$r->id]))
                                    <x-button sm wire:click="saveRoutePriceRoundTrip({{$r->id}})" label="Save" positive/>
                                    @endif

                                </div>
                            </div>
                        </td>
                        <td class="text-right">
                            <div class="form-control ">
                                <div class="input-group justify-end">
                                    <input wire:model="routePrice.{{$r->id}}" placeholder="Price" @keyup="dirty=false" class="@error('routePrice.'.$r->id) ds-disabled ds-input-error @enderror ds-input ds-input-sm ds-input-bordered">

                                    <x-button  wire:click="saveRoutePrice({{$r->id}})" label="Save" positive/>
                                </div>
                            </div>
                        </td>


                    </tr>

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
                                    <label>No defined extras</label>
                                </div>
                            </div>
                        </TD>
                    </tr>
                @endforelse
            @else
                No routes for this transfer, add new route!
            @endif
        @else
            <tr>
                <td colspan="999">
                    <div class="ds-alert ds-alert-warning">
                        <div class="flex-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 class="w-6 h-6 mx-2 stroke-current">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <label>Select a transfer to add prices!</label>
                        </div>
                    </div>
                </TD>
            </tr>
        @endif
        </tbody>

    </table>


</x-card>

