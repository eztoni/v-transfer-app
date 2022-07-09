<div x-data="{open: false,selectedLanguage: 'en'}">

    <x-card cardClasses="mb-4">
        <div class="flex justify-between">
            <h3 class="font-medium whitespace-normal text-md text-secondary-700 dark:text-secondary-400">Extra edit: <b>{{$this->extra->name}}</b></h3>

            <x-button href="{{route('extras-overview')}}"  primary label="Back to extras overview" />
        </div>
    </x-card>

    @livewire('image-gallery-uploader',['id' => $extra->id,'model' => $extra,'mediaCollectionName' => 'extraImages'])


    <div class="mt-4">

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
            <x-card title="Extra - Price">

                <x-slot name="action">
                    <x-native-select
                        label="Partner:"
                        option-label="name"
                        option-value="id"
                        :options="\App\Models\Partner::all()->map(fn ($m) => ['id'=>$m->id,'name'=>$m->name])->toArray() "
                        wire:model="partnerId"
                    />
                </x-slot>

                <x-input label="Extra Price" wire:model="extraPrice"></x-input>

                <x-slot name="footer">
                    <div class="float-right">
                        <x-button wire:click="saveExtraPrice" spinner="saveVehicle" primary label="Save Price" />
                    </div>
                </x-slot>

            </x-card>
        </div>


    </div>

</div>
