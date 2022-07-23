<div>

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


        <x-modal.card title="{{  !empty($this->partner->exists) ? 'Update':'Add' }} partner" wire:model="partnerModal">


            <x-input label="Name:" wire:model="partner.name"></x-input>
            <x-input label="Phone:" wire:model="partner.phone" placeholder="+385 91 119 9111"></x-input>
            <x-input label="Email:" wire:model="partner.email"></x-input>
            <x-select option-key-value
                      label="Destinations:"
                      wire:model="selectedDestinations"
                      multiselect
                      :options="$destinations->pluck('name','id')"></x-select>


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



