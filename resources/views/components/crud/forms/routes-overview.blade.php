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


</div>
