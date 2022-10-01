<div>
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

        @if ($transferId && $partnerId)
                <x-native-select
                    label="Partner:"
                    option-label="name"
                    option-value="id"
                    :options="\App\Models\Partner::all()->map(fn ($m) => ['id'=>$m->id,'name'=>$m->name])->toArray() "
                    wire:model="partnerId"
                />
        @endif


    </x-slot>


</x-card>


    <div class="ds-divider my-1 "></div>



@if($partnerId)

    @if ($transferId)

        @if ($this->routes->isNotEmpty())
            @forelse ($this->routes as $r)

                <div class="mb-4" x-data="{
                              open: false,
                              get isOpen() { return this.open },
                              toggle() {
                                this.open = ! this.open
                              },
                            }" wire:key="{{$r->id}}">

                    <x-card title="#{{$r->id}} - {{$r->name}} | FROM: {{$r->startingPoint->name}} | TO: {{$r->endingPoint->name}}">

                        <x-slot name="action">
                            <x-button @click="toggle()" primary><span  x-text="open ? 'Close': 'Open'"></span></x-button>
                        </x-slot>

                        <div x-show="isOpen">
                            <h1 class="text-1xl mb-1 font-bold">Date</h1>
                            <div class="grid grid-cols-4  gap-2">
                                <x-flatpickr
                                    label="Date from:"
                                    min-date=""
                                    date-format="d.m.Y"
                                    :enable-time="false"
                                    wire:model.defer="routeDateFrom.{{$r->id}}"
                                />

                                <x-flatpickr
                                    label="Date to:"
                                    min-date=""
                                    date-format="d.m.Y"
                                    :enable-time="false"
                                    wire:model.defer="routeDateTo.{{$r->id}}"
                                />
                            </div>

                            <div class="ds-divider my-1 "></div>

                            <h1 class="text-1xl mb-1 font-bold">Tax, Commission, Discount</h1>

                            <div class="grid grid-cols-4  gap-2">
                                <x-native-select
                                    label="Tax Level"
                                    placeholder="Select text level"
                                    :options="['PPOM', 'RPO']"
                                    wire:model="routeTaxLevel.{{$r->id}}"
                                />

                                <x-native-select
                                    label="Calculation Type"
                                    placeholder="Select Calculation Type"
                                    :options="['Per Item', 'Per Person']"
                                    wire:model="routeCalculationType.{{$r->id}}"
                                />

                                <x-input wire:model.debounce.300ms="routeDiscountPercentage.{{$r->id}}" label="Discount in %" placeholder="0"/>

                                <x-input wire:model.debounce.300ms="routeCommissionPercentage.{{$r->id}}" label="Commission in %" placeholder="0"/>

                            </div>

                            <div class="ds-divider my-1 "></div>

                            <h1 class="text-1xl mb-1 font-bold">One Way</h1>

                            <div class="grid grid-cols-4  gap-2 mb-4">
                                <x-input wire:change="updateRoutePrice({{$r->id}})" wire:model="routePrice.{{$r->id}}"  label="One Way Price" />

                                <x-input wire:model="routePriceWithDiscount.{{$r->id}}" label="Price with discount" disabled/>

                                <x-input wire:model="routePriceCommission.{{$r->id}}" label="Commission" disabled />
                            </div>

                            <div class="ds-divider my-1 "></div>

                            <div class="flex">
                                <h1 class="text-1xl mb-1 font-bold mr-2">Round Trip</h1>
                                <x-toggle md  wire:model="routeRoundTrip.{{$r->id}}" />
                            </div>

                            @if($routeRoundTrip[$r->id])
                                <div class="grid grid-cols-4  gap-2 mb-4">
                                    <x-input wire:change="updateRoutePriceRoundTrip({{$r->id}})"  wire:model="routePriceRoundTrip.{{$r->id}}" label="Round Trip Price" />

                                    <x-input wire:model="routeRoundTripPriceWithDiscount.{{$r->id}}" label="Round Trip with discount" disabled/>

                                    <x-input wire:model="routeRoundTripPriceCommission.{{$r->id}}" label="Round Trip Commission" disabled />
                                </div>

                            @endif


                            <div class="flex justify-end my-3">
                                <x-button wire:click="save({{$r->id}})" positive>Save</x-button>
                            </div>
                        </div>

                    </x-card>
                </div>
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
                                <label>No defined routes! Define a route first!</label>
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
                    <label>No partners defined, define a partner to setup a price!</label>
                </div>
            </div>
        </TD>
    </tr>
@endif
</div>

