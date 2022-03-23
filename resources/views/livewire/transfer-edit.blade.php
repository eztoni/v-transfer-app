<div x-data="transferSettings">
    <x-ez-card class="mb-5">

        <x-slot name="title">
            <div class="flex justify-between">
                <span>Transfer edit: {{$this->transfer->name}}</span>
                <a href="{{route('transfer-overview')}}" class="btn btn-link btn-sm">Back to transfer overview</a>

            </div>
        </x-slot>
        <x-slot name="body" class="p-2 pl-4 pt-4">

        </x-slot>
    </x-ez-card>
    @livewire('image-gallery-uploader',['id' => $transfer->id,'model' => $transfer,'mediaCollectionName' => 'transferImages'])


    <div class="mt-4">
        <x-ez-card class="h-full ">
            <x-slot name="body">
                <div class="flex md:flex-row flex-col gap-2">
                    <div class="basis-1/5 flex-shrink">

                        <p class="text-xl font-bold mb-1">
                            Transfer Settings
                        </p>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Vehicle:</span>
                                </label>

                                <select class="my-select" wire:model="vehicleId">
                                    @foreach($this->vehicles as $vehicle)
                                        <option
                                            value="{{$vehicle->id}}">{{$vehicle->name}}</option>
                                    @endforeach
                                </select>
                                @error('vehicleId')
                                <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                @enderror
                            </div>
                    </div>
                    <div class="divider divider-horizontal md:opacity-100 opacity-0"></div>
                    <div class=" basis-4/5">
                        <div class="flex justify-between ">
                            <p class="text-xl font-bold">
                                Name
                            </p>
                            <div class="tabs  ">
                                @foreach($this->companyLanguages as $languageIso)
                                    <a @click="selectedLanguage='{{$languageIso}}'" class="tab    tab-bordered "
                                       x-bind:class="selectedLanguage ==='{{$languageIso}}'?'tab-active':''">
                                        {{Str::upper($languageIso)}}
                                    </a>

                                @endforeach

                            </div>
                        </div>
                        @foreach($this->companyLanguages as $languageIso)
                            <div x-show="selectedLanguage ==='{{$languageIso}}'" x-transition:enter>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Name ({{Str::upper($languageIso)}}):</span>
                                    </label>
                                    <input wire:model="transferName.{{$languageIso}}" class="my-input  "
                                           placeholder="{{$languageIso=='en'?'ex. Dubrovnik Boat Tour':''}}">
                                    @error('transferName.'.$languageIso)
                                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                    @enderror
                                </div>
                            </div>

                        @endforeach
                            <button class="btn float-right btn-sm btn-success ml-auto mt-4"
                                    wire:click="saveTransfer">Save Transfer
                            </button>

                    </div>
                </div>

            </x-slot>
        </x-ez-card>
    </div>
    <script>
        function transferSettings() {
            return {
                selectedLanguage: 'en',
                modal: false,
                init() {

                }
            }
        }
    </script>


</div>
