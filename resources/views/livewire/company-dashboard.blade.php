<div x-data="app()">
    <div class="border shadow-sky-100 mb-4 shadow-md rounded-box bg-base-100 stat">
        <div class="stat-value">{{$company->name}}</div>
        <div class="stat-title">Contact : {{$company->contact}}</div>
        <div class="stat-desc text-secondary opacity-100">{{$company->email}}</div>
    </div>
    <div class="flex xl:gap-4 flex-col xl:flex-row  flex-col-reverse xl:flex-row">


        <x-ez-card class="basis-2/3">
            <x-slot name="body">

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Name :</span>
                    </label>
                    <input wire:model="company.name" class="input input-bordered"
                           placeholder="Name">
                    @error('company.name')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>

                <div class="form-control">

                    <label class="label">
                        <span class="label-text">Country:</span>
                    </label>
                    <x-country-select class="w-full" livewireModel="company.country_id"></x-country-select>
                    @error('company.country_id')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>

                <div class="form-control" wire:ignore>

                    <label class="label">
                        <span class="label-text">Company Languages:</span>
                    </label>
                    <select class="input input-bordered" multiple id="select2">
                        <option value="1" selected="selected" locked="locked">English</option>
                        @foreach($languages as $lang)
                            <option
                                {{$this->companyLanguages->contains('language_code',$lang->language_code) ? 'selected' : ''}} value="{{$lang->id}}">{{Str::ucfirst($lang->name)}}</option>
                        @endforeach
                    </select>
                    @error('company.default_language')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>


                <div class="flex justify-between gap-4">
                    <div class="form-control basis-1/2">
                        <label class="label">
                            <span class="label-text">Email:</span>
                        </label>
                        <input wire:model="company.email" class="input input-bordered"
                               placeholder="email">
                        @error('company.email')
                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                        @enderror
                    </div>
                    <div class="form-control basis-1/2">
                        <label class="label">
                            <span class="label-text">Contact:</span>
                        </label>
                        <input wire:model="company.contact" class="input input-bordered"
                               placeholder="Contact">
                        @error('company.contact')
                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-between gap-4">

                    <div class="form-control basis-1/2">
                        <label class="label">
                            <span class="label-text">Zip:</span>
                        </label>
                        <input wire:model="company.zip" class="input input-bordered"
                               placeholder="Zip">
                        @error('company.zip')
                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                        @enderror
                    </div>
                    <div class="form-control basis-1/2">
                        <label class="label">
                            <span class="label-text">City:</span>
                        </label>
                        <input wire:model="company.city" class="input input-bordered"
                               placeholder="City">
                        @error('company.city')
                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                        @enderror
                    </div>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Website:</span>
                    </label>
                    <input wire:model="company.website" class="input input-bordered"
                           placeholder="Website">
                    @error('company.website')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>

                <button wire:click="saveCompanyData" class="btn primary ml-auto mt-4 btn-success">Save</button>
            </x-slot>
        </x-ez-card>

        <div class="basis-1/3">

            <x-ez-card>
                <x-slot name="body">


                    <div class="flex ">
                        <div class="mt-2 mb-2 stat-value text-primary">
                            <div class="avatar online h-full">
                                <div class="w-40 h-40 rounded-full">

                                    <img src="{{!empty($this->photo)?$this->photo->temporaryUrl(): $this->imageUrl}}">
                                </div>
                            </div>
                        </div>
                        <div class=" flex flex-col w-full justify-between p-4" x-data="{filename:false}">
                            <p class=" text-2xl">Company Logo</p>
                            <p>This is your company image.<b> Upload</b> new image by pressing the button below!</p>
                            <input wire:model="photo" type="file" x-ref="photo_input" @change="filename=$el.files[0].name" class="hidden">
                            <button x-show="filename === false" class="  btn  btn-primary btn-sm w-full" @click="$refs.photo_input.click()">
                                Choose
                                image
                            </button>
                            <div class="flex w-full gap-4" x-show="filename!==false" x-transition:enter>
                                <button class="btn-square btn btn-error btn-sm"
                                        @click="$refs.photo_input.value = null;filename=false;@this.set('photo',null)">x
                                </button>
                                <button class=" flex-grow btn  btn-success btn-sm " :disabled="filename===false" @click="$refs.photo_input.value = null;filename=false" wire:click="savePhoto">
                                    <i class="fas fa-upload"></i>
                                    Upload
                                </button>
                            </div>

                            <p x-show="filename!==false" class="mb-2"><b>File: </b><span x-text="filename"></span></p>

                        </div>

                        @error('photo')
                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                        @enderror
                    </div>

                </x-slot>
            </x-ez-card>

            <div class="bg-primary text-accent-content rounded-box flex items-center p-4 shadow-xl mb-4">
                <i class="fas fa-users text-4xl mr-4 "> </i>
                <div class="flex-1 px-2"><h2 class="text-3xl font-extrabold"> {{$usersCount}}</h2>
                    <p class="text-sm text-opacity-80">Company users</p></div>
                <div class="flex-0">

                    <a href="{{route('admin.user-overview')}}" aria-label="button component"
                       class="btn btn-ghost btn-square">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             class="inline-block h-6 w-6 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="bg-primary text-accent-content rounded-box flex items-center p-4 shadow-xl mb-4">
                <i class="fas fa-car text-4xl mr-4 "> </i>
                <div class="flex-1 px-2"><h2 class="text-3xl font-extrabold"> {{$usersCount}}</h2>
                    <p class="text-sm text-opacity-80">Transfers</p></div>
                <div class="flex-0">

                    <a href="{{route('admin.user-overview')}}" aria-label="button component"
                       class="btn btn-ghost btn-square">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             class="inline-block h-6 w-6 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>


    </div>

    <script>
        function app() {
            return {
                open: false,
                init() {

                    $('#select2').select2(
                        {
                            multiple: true,
                            closeOnSelect: false
                        }
                    ).on('change', function (e) {
                        @this.
                        set('selectedLanguages', $('#select2').select2("val"))
                    }).on('select2:unselecting', function (e) {
                        if ($(e.params.args.data.element).attr('locked')) {
                            e.preventDefault();
                        }
                    });

                }
            }
        }
    </script>

</div>
