<div>
    <div class="mb-2">
        <x-card  >
            <div class="ds-stat-value">{{$company->name}}</div>
            <div class="ds-stat-title">Contact : {{$company->contact}}</div>
            <div class="ds-stat-desc text-secondary opacity-100">{{$company->email}}</div>
        </x-card>

    </div>



    <div class="flex xl:gap-2 flex-col xl:flex-row  flex-col-reverse xl:flex-row ">
        <div class="basis-3/5">
            <x-card class=" grid grid-cols-2 gap-2" title="Edit company data">
                <x-input label="Name:" wire:model.defer="company.name" />

                <x-native-select
                    label="Country:"
                    :options="\App\Models\Country::all(['id','nicename'])->mapWithKeys(fn ($model) => [$model->id => $model->nicename])->toArray()"
                    wire:model="company.country_id"
                />
                <x-select
                    label="Additional Company Languages:"
                    placeholder="Select many statuses"
                    multiselect
                    option-label="name"
                    option-value="id"
                    :options="$languages->map(fn ($m) => ['id'=>$m->id,'name'=>$m->name])->toArray()"
                    wire:model.defer="selectedLanguages"
                >
                </x-select>

                <x-input label="Email:" wire:model.defer="company.email" />

                <x-input label="Contact:" wire:model.defer="company.contact" />
                <x-input label="Zip:" wire:model.defer="company.zip" />
                <x-input label="City:" wire:model.defer="company.city" />
                <x-input label="Website:" wire:model.defer="company.website" />

                <x-slot name="footer">
                    <x-button wire:click="saveCompanyData" class="float-right" spinner= primary label="Save" />
                </x-slot>

            </x-card>
        </div>
        <div class="basis-2/5">
            <x-card  title="Set company image">
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

            </x-card>

            <div class="my-2">
                <x-card color="bg-primary-500" class=" text-accent-content rounded flex items-center p-4  mb-2">
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
                </x-card>
            </div>
        </div>


    </div>
</div>
