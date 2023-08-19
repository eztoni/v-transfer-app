<div>
    <form wire:submit.prevent=""
    <x-card title="Partners">
        <x-slot name="action">

            <x-button wire:click="addPartner" positive>Add Partner</x-button>

        </x-slot>

        <x-input wire:model="search" placeholder="Find Partner" class="mb-2"/>
        <table class="ds-table ds-table-compact w-full" wire:loading.delay.class="opacity-50">
            <thead>
            <tr>
                <th>#Id</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Destination</th>
                <th class="text-center">Update</th>
                <th class="text-center">Copy Partner</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($partners as $p)

                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->phone }}</td>
                    <td>{{ $p->email }}</td>
                    <td>{{ Str::headline($p->destinations->implode('name', ',')) }}</td>
                    <td class="text-center">
                        <x-button.circle primary icon="pencil" wire:click="updatePartner({{$p->id}})">
                        </x-button.circle>
                    </td>
                    <td class="text-center">
                        <x-button wire:click="copyPartner({{$p->id}})" positive
                        >Copy Partner</x-button>
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
                                <label>No defined partners</label>
                            </div>
                        </div>
                    </TD>
                </tr>
            @endforelse
            </tbody>


        </table>
        {{$partners->links()}}


        {{--            <div class="modal {{ $softDeleteModal ? 'modal-open fadeIn' : '' }}">--}}
        {{--                <div class="modal-box max-h-screen overflow-y-auto">--}}
        {{--                    <b>Confirm deletion?</b>--}}
        {{--                    <p>This action will delete the company.</p>--}}
        {{--                    <hr class="my-4">--}}

        {{--                    <div class="mt-4 flex justify-between">--}}
        {{--                        <button wire:click="closeSoftDeleteModal()" class="btn btn-sm ">Close</button>--}}
        {{--                        <button wire:click="softDelete()" class="btn btn-sm ">Delete</button>--}}
        {{--                    </div>--}}
        {{--                </div>--}}
        {{--            </div>--}}


        <x-modal.card title="Copy terms" wire:model="copyTermsModal">

            @if($this->partnersWithTerms->isNotEmpty())
                <x-select option-key-value
                          label="Partners:"
                          wire:model="partnerPreviewId"
                          :options="$this->partnersWithTerms->pluck('name','id')"></x-select>
            @endif
                @if($this->partnerPreviewId)
                <div x-data="{selectedLanguage:'en'}" >
                    <div class="ds-tabs justify-end mb-2">
                        @foreach($this->companyLanguages as $languageIso)
                            <a @click="selectedLanguage='{{$languageIso}}'" class="ds-tab ds-tab-bordered "
                               x-bind:class="selectedLanguage ==='{{$languageIso}}'?'ds-tab-active':''">
                                {{Str::upper($languageIso)}}
                            </a>
                        @endforeach
                    </div>
                    <p >Terms that will be copied:</p>

                @foreach($this->companyLanguages as $languageIso)
                        <div :key="{{$languageIso}}" class="mb-4" x-show="selectedLanguage ==='{{$languageIso}}'" x-transition:enter>
                            <div class="form-control"  x-data="{html:null}">

                                <p class="border rounded-lg p-2">
                                    {!!  nl2br(Arr::get($this->termsPreview,$languageIso))!!}
                                </p>
                            </div>
                        </div>

                    @endforeach

                    </div>
                @endif
                <p class="text-warning-600">By clicking Copy Terms button, all terms translations will be copied.</p>
                <x-slot name="footer">
                    <div class="flex justify-between">

                        <x-button wire:click="closeCopyTermsModal()" >Close</x-button>
                        <x-button wire:click="copyPartnerTerms()" positive
                        >Copy Terms</x-button>
                    </div>
                </x-slot>
        </x-modal.card>

        <x-modal.card title="Copy Partner" wire:model="copyPartnerModal">

            @if($this->otherOwners)
                <x-select option-key-value
                          label="Company to copy the partner to:"
                          wire:model="destinationCopyOwnerId"
                          :options="$this->otherOwners->pluck('name','id')"></x-select>
            @endif
                @if($this->partnerPreviewId)
                <div x-data="{selectedLanguage:'en'}" >
                    <div class="ds-tabs justify-end mb-2">
                        @foreach($this->companyLanguages as $languageIso)
                            <a @click="selectedLanguage='{{$languageIso}}'" class="ds-tab ds-tab-bordered "
                               x-bind:class="selectedLanguage ==='{{$languageIso}}'?'ds-tab-active':''">
                                {{Str::upper($languageIso)}}
                            </a>
                        @endforeach
                    </div>
                    <p>Terms that will be copied:</p>

                @foreach($this->companyLanguages as $languageIso)
                        <div :key="{{$languageIso}}" class="mb-4" x-show="selectedLanguage ==='{{$languageIso}}'" x-transition:enter>
                            <div class="form-control"  x-data="{html:null}">

                                <p class="border rounded-lg p-2">
                                    {!!  nl2br(Arr::get($this->termsPreview,$languageIso))!!}
                                </p>
                            </div>
                        </div>

                    @endforeach

                    </div>
                @endif
                <p class="text-warning-300">By clicking Copy Partner button, partner will be created in the destination company</p>
                <x-slot name="footer">
                    <div class="flex justify-between">

                        <x-button wire:click="closeCopyTermsModal()" >Close</x-button>
                        <x-button wire:click="copyPartnerToOwner()" positive
                        >Copy Terms</x-button>
                    </div>
                </x-slot>
        </x-modal.card>

        <x-modal.card title="{{  !empty($this->partner->exists) ? 'Update':'Add' }} partner" wire:model="partnerModal" z-index="z-30">


            <x-input label="Name:" wire:model="partner.name"></x-input>
            <x-input label="Phone:" wire:model="partner.phone" placeholder="+385 91 119 9111"></x-input>
            <x-input label="Email:" wire:model="partner.email"></x-input>
            <x-input label="Address:" wire:model="partner.address"></x-input>
            <x-select option-key-value
                      label="Destinations:"
                      wire:model="selectedDestinations"
                      multiselect
                      :options="$destinations->pluck('name','id')"></x-select>


            <hr class="my-6">

            <div x-data="{selectedLanguage:'en'}">
            <div class="ds-tabs justify-end">
                @foreach($this->companyLanguages as $languageIso)
                    <a @click="selectedLanguage='{{$languageIso}}'" class="ds-tab ds-tab-bordered "
                       x-bind:class="selectedLanguage ==='{{$languageIso}}'?'ds-tab-active':''">
                        {{Str::upper($languageIso)}}
                    </a>
                @endforeach
            </div>
            @foreach($this->companyLanguages as $languageIso)
                <div :key="{{$languageIso}}" class="mb-4" x-show="selectedLanguage ==='{{$languageIso}}'" x-transition:enter>
                    <div class="form-control"  x-data="{html:null}">
                        <x-textarea label="Terms and conditions ({{Str::upper($languageIso)}}):" wire:model="terms.{{$languageIso}}"
                        />
                    </div>
                </div>

            @endforeach
                @if($this->partnersWithTerms?->isNotEmpty() > 0)
                    <x-button sm
                              wire:click="openCopyTermsModal"
                              label="Copy terms from another partner" class="float-right"></x-button>
                @endif

            </div>
            <br/>
            <x-input label="Cancellation Opera Package ID" wire:model="partner.cancellation_package_id"></x-input>
            <x-input label="No Show Opera Package ID" wire:model="partner.no_show_package_id"></x-input>
            <x-native-select
                wire:model="partner.cf_type"
                label="Cancellation Fee Type:"
                option-key-value
                :options="$cf_types"
            />
            <x-input label="Cancellation Fee < 12 hours" wire:model="partner.cf_amount_12"></x-input>
            <x-input label="Cancellation Fee < 24 hours" wire:model="partner.cf_amount_24"></x-input>
            <x-slot name="footer">
                <div class="flex justify-between">

                    <x-button wire:click="closePartnerModal()" >Close</x-button>
                    <x-button wire:click="savePartnerData()" positive
                           >{{  !empty($this->partner->exists) ? 'Update':'Add' }}</x-button>
                </div>
            </x-slot>
        </x-modal.card>


    </x-card>
</div>



