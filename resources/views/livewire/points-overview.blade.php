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
            <th class="text-center">Copy Point</th>
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
                <td class="text-center">
                    <x-button wire:click="copyPoint({{$p->id}})" positive
                    >Copy Point</x-button>
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
                            <label>No defined points</label>
                        </div>
                    </div>
                </TD>
            </tr>
        @endforelse
        </tbody>
    </table>

    <x-modal.card wire:model="pointModal" title="{{  !empty($this->point->exists) ? 'Updating':'Adding' }} point">
        <div class="">

            @if($this->point->type != \App\Models\Point::TYPE_CITY && $this->point->exists)
            <x-native-select
                option-key-value
                placeholder="-- Select Parent Point --"
                wire:model="point.parent_point_id"
                label="Parent Point"
                :options="$this->point_options"
            />
            @endif
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

                <x-input label="PMS code:" wire:model="point.pms_code"/>
                <x-input label="PMS Class ( in case property has more than 1 class, use just any one ):" wire:model="point.pms_class"/>
                <x-input type="number" label="Current Invoice no.:" wire:model="point.fiskal_invoice_no" editable="false"/>
                <x-input  type="number" label="Business Establishment:" wire:model="point.fiskal_establishment" readolny />
                <x-input  type="number" label="Device:" wire:model="point.fiskal_device" readolny />
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



    <x-modal.card title="Copy Point" wire:model="copyPointModal">

        @if($this->otherDestinations)
            <x-select option-key-value
                      label="Destination to copy the point to:"
                      wire:model="destinationCopyPointId"
                      :options="$this->otherDestinations->pluck('name','id')"></x-select>
        @endif
        <p class="text-warning-300">By clicking Copy Point button, point will be created in the destination.</p>
        @if($this->copyPoint)
            @if($this->copyPoint->parent_point_id > 0)
                <p class="text-danger-100" style="color: darkred;font-weight:bold;">Before copying this point, make sure it's parent point is copied first.</p>
            @endif
        @endif
        <x-slot name="footer">
            <div class="flex justify-between">

                <x-button wire:click="closeCopyPointModal()" >Close</x-button>
                <x-button wire:click="copyPointToDestination()" positive
                >Copy Point</x-button>
            </div>
        </x-slot>
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
