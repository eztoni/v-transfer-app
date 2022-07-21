<div x-data="{open: false,selectedLanguage: 'en'}">

    <x-ez-card class="mb-5">

        <x-slot name="title">
            <div class="flex justify-between items-center">
                <span>Vehicle edit: <b>{{$vehicle->type}}</b></span>

                <x-button href="{{route('vehicle-overview')}}"  primary label="Back to vehicle overview" />

            </div>
        </x-slot>
        <x-slot name="body" class="p-2 pl-4 pt-4">

        </x-slot>
    </x-ez-card>


    @livewire('image-gallery-uploader',['id' => $vehicle->id,'model' => $vehicle,'mediaCollectionName' => 'vehicleImages'])


    <div class="mt-4">

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
                    <div class="form-control"  x-data="{html:null}">
                        <x-input label="Type ({{Str::upper($languageIso)}}):" wire:model="vehicleType.{{$languageIso}}"
                        />
                    </div>
                </div>

            @endforeach

            <div class="ds-divider"></div>

            <div class="form-control mt-4 mb-4">
                <x-input label="Max Occ." wire:model="vehicle.max_occ"/>
            </div>
            <div class="form-control mt-4 mb-4">
                <x-input label="Max Luggage" wire:model="vehicle.max_luggage"/>
            </div>

            <x-errors />

            <x-slot name="footer">
                <div class="float-right">
                    <x-button wire:click="saveVehicle" spinner="saveVehicle" primary label="Save Vehicle" />
                </div>
            </x-slot>

        </x-card>

    </div>
</div>
