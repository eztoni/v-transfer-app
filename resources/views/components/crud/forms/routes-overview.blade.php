<div x-data="{selectedLanguage:'en'}">
    <div class="ds-tabs">
        @foreach($this->companyLanguages as $languageIso)
            <a @click="selectedLanguage='{{$languageIso}}'" class="ds-tab ds-tab-bordered "
               x-bind:class="selectedLanguage ==='{{$languageIso}}'?'ds-tab-active':''">
                {{Str::upper($languageIso)}}
            </a>
        @endforeach
    </div>
    @foreach($this->companyLanguages as $languageIso)
        <div :key="{{$languageIso}}" class="mb-4" x-show="selectedLanguage ==='{{$languageIso}}'" x-transition:enter>
            <div class="form-control">
                <x-input label="Name ({{Str::upper($languageIso)}}):" wire:model="routeName.{{$languageIso}}"
                />
            </div>


        </div>
    @endforeach

    <x-select label="Starting point" wire:model="model.starting_point_id" searchable option-key-value :options="$this->startingPoints"></x-select>
    <x-select label="Ending point" wire:model="model.ending_point_id" searchable option-key-value :options="$this->endingPoints"></x-select>
    <x-input wire:model="model.pms_code" label="PMS code" ></x-input>
    <br/>
    <x-button  wire:click="showCopyRouteModal()" positive
    >Duplicate Route</x-button>

    <x-modal.card title="Duplicate Route" wire:model="copyRouteModal">
        <div x-data="{selectedLanguage:'en'}">
            <div class="ds-tabs">
                @foreach($this->companyLanguages as $languageIso)
                    <a @click="selectedLanguage='{{$languageIso}}'" class="ds-tab ds-tab-bordered "
                       x-bind:class="selectedLanguage ==='{{$languageIso}}'?'ds-tab-active':''">
                        {{Str::upper($languageIso)}}
                    </a>
                @endforeach
            </div>
            @foreach($this->companyLanguages as $languageIso)
                <div :key="{{$languageIso}}" class="mb-4" x-show="selectedLanguage ==='{{$languageIso}}'" x-transition:enter>
                    <div class="form-control">
                        <x-input label="Name ({{Str::upper($languageIso)}}):" wire:model="copyRouteName.{{$languageIso}}"
                        />
                    </div>


                </div>
            @endforeach

        </div>

        <p class="text-sm">By clicking Duplicate Route,all route info will be duplicated.<br/><small>The name you enter will be the name for the <b>new</b> route, and will not overwrite the existing route name.</small></p>
        <x-slot name="footer">
            <div class="flex justify-between">

                <x-button wire:click="hideCopyRouteModal()" >Close</x-button>
                <x-button wire:click="copyRoute({{$this->model->id}})" positive
                >Duplicate Route</x-button>

            </div>
        </x-slot>
    </x-modal.card>



</div>
