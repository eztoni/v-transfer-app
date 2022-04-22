<div x-data="extraSettings">
    <x-ez-card class="mb-5">

        <x-slot name="title">
            <div class="flex justify-between">
                <span>Extra edit: {{$this->extra->name}}</span>
                <a href="{{route('extras-overview')}}" class="btn btn-link btn-sm">Back to extras overview</a>

            </div>
        </x-slot>
        <x-slot name="body" class="p-2 pl-4 pt-4">

        </x-slot>
    </x-ez-card>
    @livewire('image-gallery-uploader',['id' => $extra->id,'model' => $extra,'mediaCollectionName' => 'extraImages'])


    <div class="mt-4">
        <x-ez-card class="h-full mb-4">
            <x-slot name="body">
                        <div class="flex justify-between ">
                            <p class="text-xl font-bold">
                                Extra - Information
                            </p>
                            <div class="tabs">
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
                                    <input wire:model="extraName.{{$languageIso}}" class="my-input  "
                                           placeholder="{{$languageIso=='en'?'ex. Dubrovnik Boat Tour':''}}">
                                    @error('extraName.'.$languageIso)
                                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                    @enderror
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Description ({{Str::upper($languageIso)}}):</span>
                                    </label>
                                    <input wire:model="extraDescription.{{$languageIso}}" class="my-input  "
                                           placeholder="{{$languageIso=='en'?'ex. Extra Description':''}}">
                                    @error('extraDescription.'.$languageIso)
                                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                    @enderror
                                </div>
                            </div>

                        @endforeach
                        <button class="btn btn-sm btn-success ml-auto mt-4 bottom-4 right-4"
                                wire:click="saveExtra">Save Extra
                        </button>


            </x-slot>
        </x-ez-card>


        <x-ez-card class="h-full mb-4">
            <x-slot name="body">
                <x-slot name="title" class="flex justify-between">
                    <div class="">
                        Extra - Price
                    </div>
                    <div class="">
                        <label class="label">
                            <span class="label-text">Partner:</span>
                        </label>
                        <select class="my-select select-sm" wire:model="partnerId">
                            <option value="0">{{\Auth::user()->owner->name}}</option>
                            @foreach(\App\Models\Partner::all() as $partner)
                                <option value="{{$partner->id}}">{{$partner->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </x-slot>

                <div class="form-control">

                    <label class="label">
                        <span class="label-text">Price:</span>
                    </label>
                    <input wire:model="extraPrice" class="my-input">
                    @error('extraPrice')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>

                <button class="btn btn-sm btn-success ml-auto mt-4 bottom-4 right-4"
                        wire:click="saveExtraPrice">Save Price
                </button>


            </x-slot>
        </x-ez-card>


    </div>



    <script>
        function extraSettings() {
            return {
                selectedLanguage: 'en',
                modal: false,
                init() {

                }
            }
        }
    </script>


</div>
