<div>
    <div class="flex justify-between items-center ">
        @if($this->showSearch)
            <div class="prose ">
                <h1 class=" pl-4 pt-4">
                    Transfer prices
                </h1>

            </div>

        @endif

    </div>
    <div class="ds-divider my-4"></div>

    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-3 ">

            @if($this->showSearch || ($transfer && $partner))
                <div class="flex flex-col justify-end gap-4 rounded p-4 bg-white shadow-lg border border-primary-500 pb-8">

                    @if($this->showSearch)
                        <x-native-select
                            label="Transfers:"
                            placeholder="Select a transfer"
                            option-label="name"
                            option-value="id"
                            :options="$this->transfers->map(fn ($m) => ['id'=>$m->id,'name'=>$m->name])->toArray() "
                            wire:model="transferId"
                        />
                    @endif

                    @if ($transfer && $partner)
                        <x-native-select
                            label="Partner:"
                            option-label="name"
                            option-value="id"
                            :options="\App\Models\Partner::all()->map(fn ($m) => ['id'=>$m->id,'name'=>'#'.$m->id.' '.$m->name])->toArray() "
                            wire:model="partnerId"
                        />
                    @endif



                </div>
            @endif
        </div>
        <div class="col-span-9">

            @if($transfer)
                @if($partner)

                    @if ($this->routes)
                        @forelse ($this->routes as $r)

                            <div class="mb-4" x-data="{
                              open: false,
                              get isOpen() { return this.open },
                              toggle() {
                                this.open = ! this.open
                              },
                            }" wire:key="{{$r->id}}">

                                <x-card
                                    style="padding:0px"
                                >
                                    <div
                                        @class([
                                                   'flex  w-full justify-between rounded bg-primary-500',
                                                    'border border-red-400'=>Arr::get($modelPrices,"{$r->id}.new_price")
                                                ])>
                                        <div
                                            @class([
                                                    'bg-gradient-to-r  from-primary-700 shadow-xl to-primary-300 flex py-1 rounded-tl rounded-bl items-center gap-4 text-white w-full justify-start',
                                                    ])>

                                            @if(Arr::get($modelPrices,"{$r->id}.new_price"))
                                                <x-icon name="exclamation" solid class="w-6 h-6  ml-4  text-red-400" >
                                                </x-icon>
                                            @else
                                                <x-icon name="check-circle" solid class="w-6 h-6  ml-4  text-positive-400" >
                                                </x-icon>
                                            @endif


                                            <div class="flex-grow">
                                                <span>#{{$r->id}} - {{$r->name}}</span>
                                                <hr class="my-1 opacity-50">
                                                <div class="font-bold">
                                                    {{$r->startingPoint->name.' > '.$r->endingPoint->name}}
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="border-l border-gray-400 bg-primary-500 rounded-r flex items-center text-white justify-center px-6 cursor-pointer"

                                            @click="toggle()">
                                            <div class="transition-all duration-300"
                                                 x-bind:class="{'rotate-180':open}"
                                            >
                                                <x-icon

                                                    name='arrow-down' class="w-6 h-6   "></x-icon>
                                            </div>
                                        </div>

                                    </div>


                                    <div x-transition.duration.300ms class="p-4 relative" x-show="isOpen">


                                        <h1 class="text-1xl mb-1 font-bold">Date & Opera Mapping</h1>
                                        <div class="grid grid-cols-4  gap-2">
                                            <x-flatpickr
                                                label="Date from:"
                                                min-date=""
                                                date-format="d.m.Y"
                                                :enable-time="false"
                                                wire:model.defer="modelPrices.{{$r->id}}.date_from"
                                            />

                                            <x-flatpickr
                                                label="Date to:"
                                                min-date=""
                                                date-format="d.m.Y"
                                                :enable-time="false"
                                                wire:model.defer="modelPrices.{{$r->id}}.date_to"
                                            />
                                            <x-input wire:model="modelPrices.{{$r->id}}.opera_package_id"
                                                     label="Opera Package ID" placeholder="PCKG{{$r->id}}" />
                                        </div>


                                        <div class="ds-divider my-1 "></div>

                                        <h1 class="text-1xl mb-1 font-bold">Tax, Commission, Discount</h1>

                                        <div class="grid grid-cols-4  gap-2">
                                            <x-native-select
                                                label="Tax Level"
                                                placeholder="Select text level"
                                                :options="['PPOM', 'RPO']"
                                                wire:model="modelPrices.{{$r->id}}.tax_level"
                                            />

                                            <x-native-select
                                                label="Calculation Type"
                                                placeholder="Select Calculation Type"
                                                :options="['Per Item', 'Per Person']"
                                                wire:model="modelPrices.{{$r->id}}.calculation_type"
                                            />

                                            <x-input wire:model.debounce.300ms="modelPrices.{{$r->id}}.discount"
                                                     label="Discount in %" placeholder="0"/>

                                            <x-input wire:model.debounce.300ms="modelPrices.{{$r->id}}.commission"
                                                     label="Commission in %" placeholder="0"/>

                                        </div>

                                        <div class="ds-divider my-1 "></div>

                                        <h1 class="text-1xl mb-1 font-bold">One Way</h1>

                                        <div class="grid grid-cols-4  gap-2 mb-4">
                                            <x-input wire:model.debounce.500ms="modelPrices.{{$r->id}}.price"
                                                     label="One Way Price"
                                                     hint="Format example: 10.000,00"
                                            />

                                            <x-input wire:model="modelPrices.{{$r->id}}.price_with_discount"
                                                     label="Price with discount" disabled/>

                                            <x-input wire:model="modelPrices.{{$r->id}}.price_with_commission"
                                                     label="Commission" disabled/>
                                        </div>

                                        <div class="ds-divider my-1 "></div>

                                        <div class="flex">
                                            <h1 class="text-1xl mb-1 font-bold mr-2">Round Trip</h1>
                                            <x-toggle md wire:model="modelPrices.{{$r->id}}.round_trip"/>
                                        </div>

                                        @if(Arr::get($modelPrices,"{$r->id}.round_trip"))
                                            <div class="grid grid-cols-4  gap-2 mb-4">
                                                <x-input
                                                    hint="Format example: 10.000,00"
                                                    wire:model.debounce.500ms="modelPrices.{{$r->id}}.price_round_trip"
                                                    label="Round Trip Price"/>

                                                <x-input
                                                    wire:model="modelPrices.{{$r->id}}.round_trip_price_with_discount"
                                                    label="Round Trip with discount" disabled/>

                                                <x-input
                                                    wire:model="modelPrices.{{$r->id}}.round_trip_price_with_commission"
                                                    label="Round Trip Commission" disabled/>
                                            </div>

                                        @endif


                                        <div class="flex justify-between my-3 gap-4 items-center">
                                            @if(Arr::get($modelPrices,"{$r->id}.new_price"))
                                                <x-badge negative outline>

                                                    <x-icon name="exclamation" class="w-4 h-4" >

                                                    </x-icon>
                                                    <div class="">
                                                        Price not set
                                                        <br>
                                                        Please set the price to make transfer for this partner available for booking

                                                    </div>

                                                </x-badge>
                                            @else
                                                <p></p>
                                            @endif
                                            <x-button wire:click="save({{$r->id}})" icon="save" positive>Save</x-button>
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
                            <div class="flex flex-col justify-end gap-4 rounded p-4 bg-white shadow-lg border border-primary-500 pb-8">
                                <div class="ds-alert ds-alert-warning">
                                    <div class="flex-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             class="w-6 h-6 mx-2 stroke-current">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        <label>{{$partnerMessage}}</label>
                                    </div>
                                </div>
                            </div>
                        </td>
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
                                <label>Select a transfer to add prices!</label>
                            </div>
                        </div>
                    </TD>
                </tr>

            @endif
        </div>
    </div>




</div>

