<div x-data="{open: false,selectedLanguage: 'en'}">

    <x-card cardClasses="mb-4">
        <div class="flex justify-between">
            <h3 class="font-medium whitespace-normal text-md text-secondary-700 dark:text-secondary-400">Transfer details</h3>

            <x-button href="{{route('transfer-overview')}}"  primary label="Back to transfer overview" />
        </div>
    </x-card>



    @livewire('image-gallery-uploader',['id' => $transfer->id,'model' => $transfer,'mediaCollectionName' => 'transferImages'])


    <div class="mt-4">

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
                    <div class="form-control"  x-data="{html:null}">
                        <x-input label="Name ({{Str::upper($languageIso)}}):" wire:model="transferName.{{$languageIso}}"
                        />
                    </div>
                </div>

            @endforeach

            <div class="ds-divider"></div>

            <div class="form-control mt-4 mb-4">
                <x-native-select
                    label="Vehicle:"
                    option-label="name"
                    option-value="id"
                    :options="$this->vehicles->map(fn ($m) => ['id'=>$m->id,'name'=>$m->type])->toArray() "
                    wire:model="vehicleId"
                />
            </div>

            <div class="form-control mb-4">
                <x-native-select
                    label="Destination:"
                    placeholder="Select a destination"
                    option-label="name"
                    option-value="id"
                    :options="\App\Models\Destination::all()->map(fn ($m) => ['id'=>$m->id,'name'=>$m->name])->toArray() "
                    wire:model="vehicleId"
                />
            </div>



            <x-errors />

            <x-slot name="footer">
                <div class="float-right">
                    <x-button wire:click="saveTransfer" spinner="saveTransfer" primary label="Save Transfer" />
                </div>
            </x-slot>

        </x-card>



        <div class="mt-4">
        @livewire('transfer-prices',['transferId' => $transfer->id,'showSearch' => false])
        </div>
    </div>

</div>
