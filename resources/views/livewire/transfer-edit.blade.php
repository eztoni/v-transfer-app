<div>
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


    <div class="">
        <x-ez-card class="h-full ">
            <x-slot name="body">
                <div class="">

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
                        @if($this->transfer->isDirty() )
                            <button class="btn float-right btn-sm btn-success ml-auto mt-4"
                                    wire:click="saveTransfer">Save Transfer

                            </button>

                        @endif

                    </div>

            </x-slot>
        </x-ez-card>
    </div>





</div>
