<div x-data="{open: false,selectedLanguage: 'en'}">
    <x-settings-layout.layout
        default-handle="1">
        <x-slot:tabHeader>
            <x-card cardClasses="mb-4 " >
                <div class="flex justify-end items-center">
                    <x-button href="{{route('transfer-overview')}}"  primary label="Back to transfer overview" />
                </div>
            </x-card>
        </x-slot:tabHeader>
        <x-slot:navItems>
            <x-settings-layout.tab-nav-item icon="eye" label="Details" handle="1"/>
            <x-settings-layout.tab-nav-item  icon="photograph" label="Images" handle="2"/>
            <x-settings-layout.tab-nav-item icon="information-circle" label="Info" handle="3"/>
            <x-settings-layout.tab-nav-item  icon="currency-euro" label="Prices" handle="4"/>

        </x-slot:navItems>
        <x-slot:navHeader>
            <x-select
                label="Transfer:"
                option-label="name"
                option-value="id"
                :searchable="true"
                :clearable="false"
                min-items-for-search="2"
                wire:model="transferId"
                :options="$this->allTransfersForSelect"
            />
        </x-slot:navHeader>

        <x-settings-layout.tab
        handle="1">
            <x-card cardClasses="mb-4 " >
                <article class="prose max-w-full">
                    <h1>Transfer: {{$transfer->name}}</h1>

                    <p class="flex gap-2 items-center">
                        <x-icon name="truck" class="w-5 h-5"/>
                        Vehicle:
                        <a href="{{route('vehicle-edit',['vehicle'=>$transfer->vehicle->id])}}" >
                        {{$transfer->vehicle->type}}
                        </a>
                    </p>
<p class="flex gap-2 items-center">
                        <x-icon name="location-marker" class="w-5 h-5"/>
                        Destination:
                        {{$transfer->destination->name}}
                    </p>

                </article>

                <hr class="my-4">
                <div class="prose mb-4">
                    <h2>Images:</h2>
                </div>

                <div class="ds-carousel rounded-box">
                    @forelse($this->transfer->getMedia('transferImages') as $media )
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

        <x-settings-layout.tab
            handle="2">
        <livewire:image-gallery-uploader wire:key="{{Str::random()}}"  :id="$transfer->id" :model="$transfer" media-collection-name="transferImages">

        </livewire:image-gallery-uploader>

        </x-settings-layout.tab>
        <x-settings-layout.tab
            handle="3">
            <x-card title="Transfer Settings">

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
                            <x-input label="Name ({{Str::upper($languageIso)}}):" wire:model="transferName.{{$languageIso}}"
                            />
                        </div>
                    </div>

                @endforeach

                <div class="ds-divider"></div>

                <div class="form-control mt-4 mb-4 max-w-sm">
                    <x-native-select
                        label="Vehicle:"
                        option-label="name"
                        option-value="id"
                        :options="$this->vehicles->map(fn ($m) => ['id'=>$m->id,'name'=>$m->type])->toArray() "
                        wire:model="vehicleId"
                    />
                </div>

                <div class="form-control mb-4 max-w-sm">
                    <x-native-select
                        label="Destination:"
                        placeholder="Select a destination"
                        option-label="name"
                        option-value="id"
                        :options="\App\Models\Destination::all()->map(fn ($m) => ['id'=>$m->id,'name'=>$m->name])->toArray() "
                        wire:model="destinationId"
                    />
                </div>



                <x-errors />

                <x-slot name="footer">
                    <div class="float-right">
                        <x-button wire:click="saveTransfer" spinner="saveTransfer" primary label="Save Transfer" />
                    </div>
                </x-slot>

            </x-card>
        </x-settings-layout.tab>

        <x-settings-layout.tab handle="4">
            @livewire('transfer-prices',['transferId' => $transfer->id,'showSearch' => false])

        </x-settings-layout.tab>
    </x-settings-layout.layout>


</div>
