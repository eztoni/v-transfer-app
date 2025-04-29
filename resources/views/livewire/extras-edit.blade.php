<div x-data="{selectedLanguage: 'en'}">
    <x-settings-layout.layout
    default-handle="1">
        <x-slot:tabHeader>
            <x-card cardClasses="mb-4 " >
                <div class="flex justify-end">
                    <x-button href="{{route('extras-overview')}}"  primary label="Back to extras overview" />
                </div>
            </x-card>
        </x-slot:tabHeader>
        <x-slot:navHeader>
            <x-select
                label="Extra:"
                option-label="name"
                option-value="id"
                :searchable="true"
                class="flex-grow hidden"
                :clearable="false"
                min-items-for-search="2"
                wire:model="extraId"
                :options="$this->allExtrasForSelect"
            />
        </x-slot:navHeader>
        <x-slot:navItems>
            <x-settings-layout.tab-nav-item icon="eye" label="Details" handle="1"/>
            <x-settings-layout.tab-nav-item  icon="photograph" label="Images" handle="2"/>
            <x-settings-layout.tab-nav-item icon="information-circle" label="Info" handle="3"/>
            <x-settings-layout.tab-nav-item  icon="currency-euro" label="Prices" handle="4"/>
        </x-slot:navItems>

        <x-settings-layout.tab handle="1">
            <x-card cardClasses="mb-4 " >
                <article class="prose max-w-full">
                    <h1>Extra: {{$extra->name}}</h1>
                    <p>{{$extra->description}}</p>
                    <!-- ... -->
                </article>
                <hr class="my-4">
                <div class="prose mb-4">
                    <h2>Images:</h2>
                </div>

                <div class="ds-carousel rounded-box">
                    @forelse($this->extra->getMedia('extraImages') as $media )
                        <div class="ds-carousel-item">
                            <img
                                wire:key="{{Str::random()}}"
                                src="{{$media->getFullUrl('thumb')}}" alt="Burger" />
                        </div>
                    @empty
                        <div class="col-span-10">
                            <x-input-alert  type='warning'>Please upload at least one image!</x-input-alert>
                        </div>
                    @endforelse
                </div>
            </x-card>
        </x-settings-layout.tab>
        <x-settings-layout.tab handle="2">
            <livewire:image-gallery-uploader :id="$extra->id" :model="$extra" :media-collection-name="'extraImages'" wire:key="{{\Illuminate\Support\Str::random()}}"></livewire:image-gallery-uploader>
        </x-settings-layout.tab>
        <x-settings-layout.tab handle="3">
            <x-card title="Extra - Information" >
                <x-slot name="action">
                    <div class="ds-tabs">
                        @foreach($this->companyLanguages as $languageIso)
                            <a @click="selectedLanguage='{{$languageIso}}'" class="ds-tab ds-tab-bordered "
                               x-bind:class="selectedLanguage ==='{{$languageIso}}'?'ds-tab-active':''">
                                {{Str::upper($languageIso)}}
                            </a>
                        @endforeach
                    </div>
                </x-slot>

                @foreach($this->companyLanguages as $languageIso)
                    <div :key="{{$languageIso}}" class="mb-4" x-show="selectedLanguage ==='{{$languageIso}}'" x-transition:enter>
                        <div class="form-control">
                            <x-input label="Name ({{Str::upper($languageIso)}}):" wire:model="extraName.{{$languageIso}}"
                            />
                        </div>

                        <div class="form-control">
                            <x-input label="Description ({{Str::upper($languageIso)}}):" wire:model="extraDescription.{{$languageIso}}"
                            />
                        </div>
                    </div>
                @endforeach
                <div class="flex items-center">
                    <input type="checkbox" id="hidden" wire:model="hidden" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="hidden" class="ml-2 text-gray-700 text-sm">Extra hidden for Booking</label>
                </div>

                <x-errors />

                <x-slot name="footer">
                    <div class="float-right">
                        <x-button wire:click="saveExtra" spinner="saveExtra" primary label="Save Extra" />
                    </div>
                </x-slot>

            </x-card>
        </x-settings-layout.tab>
        <x-settings-layout.tab handle="4">

            <div>
                <div class="ds-divider my-4"></div>

                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-3 ">
                        <div class="flex flex-col justify-end gap-4 rounded p-4 bg-white shadow-lg border border-primary-500 pb-8">

                            @if ($partnerId)
                                <x-native-select
                                    label="Partner:"
                                    option-label="name"
                                    option-value="id"
                                    :options="\App\Models\Partner::all()->map(fn ($m) => ['id'=>$m->id,'name'=>$m->name])->toArray() "
                                    wire:model="partnerId"
                                />
                            @endif
                        </div>


                    </div>
                    <div class="col-span-9">

                        @if($partnerId)
                            @if($partnerId)


                                        <div class="mb-4" x-data="{
                              open: true,
                              get isOpen() { return this.open },
                              toggle() {
                                this.open = ! this.open
                              },
                            }" wire:key="{{$partnerId}}">

                                            <x-card
                                                style="padding:0px"
                                            >
                                                <div
                                                    @class([
                                                               'flex  w-full justify-between rounded bg-primary-500',
                                                                'border border-red-400'=>Arr::get($modelPrices,"{$this->partnerId}.new_price")
                                                            ])>
                                                    <div
                                                        @class([
                                                                'bg-gradient-to-r  from-primary-700 shadow-xl to-primary-300 flex py-1 rounded-tl rounded-bl items-center gap-4 text-white w-full justify-start',
                                                                ])>

                                                        @if(Arr::get($modelPrices,"{$this->partnerId}.new_price"))
                                                            <x-icon name="exclamation" solid class="w-6 h-6  ml-4  text-red-400" >
                                                            </x-icon>
                                                        @else
                                                            <x-icon name="check-circle" solid class="w-6 h-6  ml-4  text-positive-400" >
                                                            </x-icon>
                                                        @endif


                                                        <div class="flex-grow">
                                                            <span>Partner : {{$this->partner->name}}</span>
                                                            <hr class="my-1 opacity-50">
                                                            <div class="font-bold">
                                                                {{$extraName['en']}}
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


                                                    <h1 class="text-1xl mb-1 font-bold">Date</h1>
                                                    <div class="grid grid-cols-4  gap-2">
                                                        <x-flatpickr
                                                            label="Date from:"
                                                            min-date=""
                                                            date-format="d.m.Y"
                                                            :enable-time="false"
                                                            wire:model.defer="modelPrices.{{$this->partnerId}}.date_from"
                                                        />

                                                        <x-flatpickr
                                                            label="Date to:"
                                                            min-date=""
                                                            date-format="d.m.Y"
                                                            :enable-time="false"
                                                            wire:model.defer="modelPrices.{{$this->partnerId}}.date_to"
                                                        />
                                                    </div>

                                                    <div class="ds-divider my-1 "></div>

                                                    <h1 class="text-1xl mb-1 font-bold">Tax, Commission, Discount</h1>

                                                    <div class="grid grid-cols-4  gap-2">
                                                        <x-native-select
                                                            label="Tax Level"
                                                            placeholder="Select text level"
                                                            :options="['PPOM', 'RPO']"
                                                            wire:model="modelPrices.{{$this->partnerId}}.tax_level"
                                                        />

                                                        <x-native-select
                                                            label="Calculation Type"
                                                            placeholder="Select Calculation Type"
                                                            :options="['Per Item', 'Per Person']"
                                                            wire:model="modelPrices.{{$this->partnerId}}.calculation_type"
                                                        />

                                                        <x-input wire:model.debounce.300ms="modelPrices.{{$this->partnerId}}.discount"
                                                                 label="Discount in %" placeholder="0"/>

                                                        <x-input wire:model.debounce.300ms="modelPrices.{{$this->partnerId}}.commission"
                                                                 label="Commission in %" placeholder="0"/>

                                                    </div>

                                                    <div class="ds-divider my-1 "></div>

                                                    <h1 class="text-1xl mb-1 font-bold">Extra</h1>

                                                    <div class="grid grid-cols-4  gap-2 mb-4">
                                                        <x-input wire:model.debounce.500ms="modelPrices.{{$this->partnerId}}.price"
                                                                 label="Extra Price"
                                                                 hint="Format example: 10.000,00"
                                                        />

                                                        <x-input wire:model="modelPrices.{{$this->partnerId}}.price_with_discount"
                                                                 label="Price with discount" disabled/>

                                                        <x-input wire:model="modelPrices.{{$this->partnerId}}.price_with_commission"
                                                                 label="Commission" disabled/>
                                                    </div>

                                                    <div class="ds-divider my-1 "></div>

                                                    <h1 class="text-1xl mb-1 font-bold">Opera Mapping</h1>

                                                    <x-input wire:model.debounce.500ms="modelPrices.{{$this->partnerId}}.package_id"
                                                             label="Package ID"
                                                             hint="If the mapping code is set, this amenity will be displayed as a separate item on the booking confirmation."
                                                    />

                                                    <div class="flex justify-between my-3 gap-4 items-center">
                                                        @if(Arr::get($modelPrices,"{$this->partnerId}.new_price"))
                                                            <x-badge negative outline>

                                                                <x-icon name="exclamation" class="w-4 h-4" >

                                                                </x-icon>
                                                                <div class="">
                                                                    Price not set
                                                                    <br>
                                                                    Please set the price to make extra for this partner available for booking
                                                                </div>

                                                            </x-badge>
                                                        @else
                                                            <p></p>
                                                        @endif
                                                        <x-button wire:click="save()" icon="save" positive>Save</x-button>
                                                    </div>
                                                </div>


                                            </x-card>
                                        </div>
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
                                                <label>Select a partner to add prices!</label>
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
                                            <label>Select a transfer to add prices!</label>
                                        </div>
                                    </div>
                                </TD>
                            </tr>

                        @endif
                    </div>
                </div>

            </div>

        </x-settings-layout.tab>
    </x-settings-layout.layout>

</div>
