<div x-data="vehicleSettings()">

    <x-ez-card class="mb-5">

        <x-slot name="title">
            <div class="flex justify-between">
                <span>Vehicle edit: {{$vehicle->name}}</span>
                <a href="{{route('vehicle-overview')}}" class="btn btn-link btn-sm">Back to vehicle overview</a>

            </div>
        </x-slot>
        <x-slot name="body" class="p-2 pl-4 pt-4">

        </x-slot>
    </x-ez-card>


    @livewire('image-gallery-uploader',['id' => $vehicle->id,'model' => $vehicle,'mediaCollectionName' => 'vehicleImages'])


    <div class="mt-4">
        <x-ez-card class="h-full ">
            <x-slot name="body">
                <div class="flex md:flex-row flex-col gap-2">
                    <div class="basis-1/5 flex-shrink">

                        <p class="text-xl font-bold mb-1">
                            Vehicle Settings
                        </p>
                        <div class="form-control">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Type : </span>
                                </label>
                                <input wire:model="vehicle.type" class="input input-bordered"
                                       placeholder="Vehicle Type">
                                @error('vehicle.type')
                                <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                @enderror
                            </div>
                        </div>

                        <div class="form-control">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Max Occ : </span>
                                </label>
                                <input wire:model="vehicle.max_occ" class="input input-bordered"
                                       placeholder="Max Occ.">
                                @error('vehicle.max_occ')
                                <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                @enderror
                            </div>
                        </div>

                        <div class="form-control">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Max Luggage : </span>
                                </label>
                                <input wire:model="vehicle.max_luggage" class="input input-bordered"
                                       placeholder="Max Luggage">
                                @error('vehicle.max_luggage')
                                <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                @enderror
                            </div>
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
                                    <input wire:model="vehicleName.{{$languageIso}}" class="my-input  "
                                           placeholder="{{$languageIso=='en'?'ex. Dubrovnik Boat Tour':''}}">
                                    @error('vehicleName.'.$languageIso)
                                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                    @enderror
                                </div>
                            </div>

                        @endforeach
                        <button class="btn float-right btn-sm btn-success ml-auto mt-4 absolute bottom-5 right-5"
                                wire:click="saveVehicle">Save Vehicle
                        </button>

                    </div>
                </div>

            </x-slot>
        </x-ez-card>
    </div>
    <script>
        function vehicleSettings() {
            return {
                selectedLanguage: 'en',
                modal: false,
                init() {

                }
            }
        }
    </script>

</div>
