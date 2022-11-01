<div x-data="{open: false,selectedLanguage: 'en'}">
    <x-settings-layout.layout
        default-handle="1">
        <x-slot:tabHeader>
            <x-card cardClasses="mb-4 " >
                <div class="flex justify-end items-center">
                    <x-button href="{{route('vehicle-overview')}}"  primary label="Back to vehicle overview" />
                </div>
            </x-card>
        </x-slot:tabHeader>
        <x-slot:navItems>
            <x-settings-layout.tab-nav-item icon="eye" label="Details" handle="1"/>
            <x-settings-layout.tab-nav-item  icon="photograph" label="Images" handle="2"/>
            <x-settings-layout.tab-nav-item icon="information-circle" label="Info" handle="3"/>
        </x-slot:navItems>
        <x-slot:navHeader>
            <x-select
                label="Vehicle:"
                option-label="name"
                option-value="id"
                :searchable="true"
                class="flex-grow"
                :clearable="false"
                min-items-for-search="2"
                wire:model="vehicleId"
                :options="$this->allVehiclesForSelect"
            />
        </x-slot:navHeader>
        <x-settings-layout.tab handle="1">
            <x-card cardClasses="mb-4 " >
                <article class="prose max-w-full">
                    <h1>Vehicle: {{$vehicle->type}}</h1>
                    <p class="flex gap-2 items-center">
                        <x-icon name="shopping-bag" class="w-5 h-5"/>
                        Maximum amount of luggage:
                        {{$vehicle->max_luggage}}</p>
                    <p class="flex gap-2 items-center">
                        <x-icon name="users" class="w-5 h-5"/>
                        Maximum number of passengers:
                        {{$vehicle->max_occ}}</p>
                    <!-- ... -->
                </article>
                <hr class="my-4">
                <div class="prose mb-4">
                    <h2>Images:</h2>
                </div>

                <div class="ds-carousel rounded-box">
                    @forelse($this->vehicle->getMedia('vehicleImages') as $media )
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
            <livewire:image-gallery-uploader
                wire:key="{{Str::random()}}"
                :id="$vehicle->id" :model="$vehicle" :media-collection-name="'vehicleImages'"/>


        </x-settings-layout.tab>
        <x-settings-layout.tab handle="3">

                <x-card title="Vehicle Settings">

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
                            <div class="form-control max-w-sm"  x-data="{html:null}">
                                <x-input label="Type ({{Str::upper($languageIso)}}):" wire:model="vehicleType.{{$languageIso}}"
                                />
                            </div>
                        </div>

                    @endforeach

                    <div class="ds-divider"></div>

                    <div class="form-control mt-4 mb-4 max-w-sm">
                        <x-input label="Max Occ." wire:model="vehicle.max_occ"/>
                    </div>
                    <div class="form-control mt-4 mb-4 max-w-sm">
                        <x-input label="Max Luggage"  wire:model="vehicle.max_luggage"/>
                    </div>

                    <x-errors />

                    <x-slot name="footer">
                        <div class="float-right">
                            <x-button wire:click="saveVehicle" spinner="saveVehicle" primary label="Save Vehicle" />
                        </div>
                    </x-slot>

                </x-card>

        </x-settings-layout.tab>

    </x-settings-layout.layout>






</div>
