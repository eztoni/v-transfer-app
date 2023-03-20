<div x-data="{selectedLanguage:'en'}">
<x-card title="{{$destination->name}} - Pickup & Dropoff Points">
    <x-slot name="action" >
        <x-button wire:click="addPoint" positive>Add Point</x-button>
    </x-slot>
    <x-input type="text" wire:model="search" class="my-2" placeholder="Search Points"/>

    <table class="ds-table ds-table-compact w-full" wire:loading.delay.class="opacity-50">
        <thead>
        <tr>
            <th>#ID</th>
            <th>Name</th>
            <th>Internal Name</th>
            <th class="text-center">Update</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($points as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->internal_name }}</td>
                <td class="text-center">
                    <x-button.circle primary wire:click="updatePoint({{$p->id}})" icon="pencil">
                    </x-button.circle>
                </td>


            </tr>
        @empty
            <tr>
                <td colspan="999">
                    <div class="ds-alert ds-alert-warning">
                        <div class="flex-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 class="w-6 h-6 mx-2 stroke-current">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <label>No defined vehicles</label>
                        </div>
                    </div>
                </TD>
            </tr>
        @endforelse
        </tbody>
    </table>

    <x-modal.card wire:model="pointModal" title="{{  !empty($this->point->exists) ? 'Updating':'Adding' }} point">
        <div class="">


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
                        <x-input label="Name ({{Str::upper($languageIso)}}):" wire:model="pointName.{{$languageIso}}"
                        />
                    </div>
                </div>
            @endforeach



            <x-input label="Internal Name:" wire:model="point.internal_name"
            hint="This name will be shown in the system to the users."
            />
            <x-input label="Description:" wire:model="point.description"/>
            <x-input label="Address:" wire:model="point.address"/>

            <x-native-select
                placeholder="Type"
                wire:model="point.type"
                label="Type:"
                :options="\App\Models\Point::TYPE_ARRAY"
            />


            @if($this->point->type == \App\Models\Point::TYPE_ACCOMMODATION)

                <x-input label="Reception Email:" wire:model="point.reception_email"/>

                <x-input label="PMS Class:" wire:model="point.pms_class"/>
                <x-input label="PMS code:" wire:model="point.pms_code"/>
                <x-native-select
                    wire:model="point.fiskal_id"
                    label="Invoice Certificate ( Fiskalizacija )"
                    :options="[
                                ['name' => 'Demo Certificate',  'id' => 0],
                                ['name' => 'Valamar Certifikat',  'id' => 1],
                                ['name' => 'Imperial Certifikat',  'id' => 2],
                            ]"
                    option-label="name"
                    option-value="id"
                />
            @else
                <x-input label="PMS code:" wire:model="point.pms_code"/>
            @endif

            <br>
            <x-errors only="not_unique" />
            <x-slot name="footer">



                <div class="mt-4 flex justify-between">
                    <div >
                        <x-button wire:click="$set('importPoint',true)"
                                  icon="cloud-download"
                                  label="Import from Api" />

                    </div>
                    <div >
                        <x-button wire:click="closePointModal()" icon="x" >Close</x-button>
                        <x-button wire:click="savePointData()" icon="save" positive
                        >{{  !empty($this->point->exists) ? 'Update':'Add' }}</x-button>
                    </div>

                </div>
            </x-slot>


        </div>

    </x-modal.card>


    <x-modal.card blur max-width="6xl" wire:model="importPoint" title="Import point">

        @if($this->valamarPropertiesFromApi)


            <div class="max-h-96 overflow-y-scroll">
                <table class="ds-table ds-table-compact w-full  ">
                    <thead>
                    <tr>
                        <th>Opera code</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Address</th>
                        <th>Import</th>

                    </tr>
                    </thead>
                    <tbody>

                    @foreach($this->valamarPropertiesFromApi as $k=> $r)

                        <tr>
                           <th>{{\Illuminate\Support\Arr::get($r,'propertyOperaCode')??'-'}}</th>
                            <th>{{\Illuminate\Support\Arr::get($r,'name')}}</th>
                            <th>{{\Illuminate\Support\Arr::get($r,'class')}}</th>
                            <th>{{\Illuminate\Support\Arr::get($r,'address')?:' - '}}</th>

                            <td>
                                <x-button.circle sm wire:click="setImportData('{{$k}}')"
                                                 icon="cloud-download"/>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        @endif

        <x-slot name="footer">
            <div class="float-right">
                <x-button wire:click="$set('importPoint',false)" label="Close" />
            </div>

        </x-slot>

    </x-modal.card>



    {{$points->links()}}

</x-card>

</div>
