<div x-data="{open: false,selectedLanguage: 'en'}">

    <x-card cardClasses="mb-4">
        <div class="flex justify-between">
            <h3 class="font-medium whitespace-normal text-md text-secondary-700 dark:text-secondary-400">Extra edit: <b>{{$this->extra->name}}</b></h3>

            <x-button href="{{route('extras-overview')}}"  primary label="Back to extras overview" />
        </div>
    </x-card>

    @livewire('image-gallery-uploader',['id' => $extra->id,'model' => $extra,'mediaCollectionName' => 'extraImages'])


    <x-card title="Extra - Information">

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

        <x-errors />

        <x-slot name="footer">
            <div class="float-right">
                <x-button wire:click="saveExtra" spinner="saveVehicle" primary label="Save Extra" />
            </div>
        </x-slot>

    </x-card>

    <div class="mt-4">
        <x-card title="Extra - Partner ">
            @if($partnerId)
                <x-slot name="action">
                    <x-native-select
                        label="Partner:"
                        option-label="name"
                        option-value="id"
                        :options="\App\Models\Partner::all()->map(fn ($m) => ['id'=>$m->id,'name'=>$m->name])->toArray() "
                        wire:model="partnerId"
                    />
                </x-slot>
            @else
                <div class="ds-alert ds-alert-warning">
                    <div class="flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             class="w-6 h-6 mx-2 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <label>No partners defined! First define a partner to set up a price for this extra!</label>
                    </div>
                </div>
            @endif

        </x-card>
    </div>


    <div class="ds-divider my-1 "></div>


    <div class="mb-4">

        <x-card title="Price Setup for extra - {{$extraName['en']}}">

            <div>
                <h1 class="text-1xl mb-1 font-bold">Date</h1>
                <div class="grid grid-cols-4  gap-2">
                    <x-flatpickr
                        label="Date from:"
                        min-date=""
                        date-format="d.m.Y"
                        :enable-time="false"
                        wire:model.defer="extraDateFrom"
                    />

                    <x-flatpickr
                        label="Date to:"
                        min-date=""
                        date-format="d.m.Y"
                        :enable-time="false"
                        wire:model.defer="extraDateTo"
                    />
                </div>

                <div class="ds-divider my-1 "></div>

                <h1 class="text-1xl mb-1 font-bold">Tax, Commission, Discount</h1>

                <div class="grid grid-cols-4  gap-2">
                    <x-native-select
                        label="Tax Level"
                        placeholder="Select text level"
                        :options="['PPOM', 'RPO']"
                        wire:model="extraTaxLevel"
                    />

                    <x-native-select
                        label="Calculation Type"
                        placeholder="Select Calculation Type"
                        :options="['Per Item', 'Per Person']"
                        wire:model="extraCalculationType"
                    />

                    <x-input wire:change="updateExtraPrice" wire:model.debounce.300ms="extraDiscountPercentage" label="Discount in %" placeholder="0" />

                    <x-input wire:change="updateExtraPrice" wire:model.debounce.300ms="extraCommissionPercentage" label="Commission in %" placeholder="0" />

                </div>

                <div class="ds-divider my-1 "></div>

                <h1 class="text-1xl mb-1 font-bold">One Way</h1>

                <div class="grid grid-cols-4  gap-2 mb-4">
                    <x-input wire:change="updateExtraPrice" wire:model="extraPrice"  label="Extra Price" />

                    <x-input wire:model="extraPriceWithDiscount" label="Price with discount" disabled/>

                    <x-input wire:model="extraPriceCommission" label="Commission" disabled />
                </div>

                <div class="flex justify-end my-3">
                    <x-button wire:click="save()" positive>Save</x-button>
                </div>
            </div>

        </x-card>
    </div>



</div>
