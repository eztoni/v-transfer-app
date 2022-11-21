<div><x-card title="Transfers">
        <x-slot name="action">
            <x-button wire:click="addTransfer" positive>Add Transfer</x-button>
        </x-slot>

            <x-input type="text" wire:model="search" class="my-2" placeholder="Find Transfer"/>

        <table class="ds-table ds-table-zebra ds-table-compact w-full" wire:loading.delay.class="opacity-50">
            <thead>
            <tr>
                <th class="w-16">#Id</th>
                <th class="w-24 text-center">Image</th>
                    <th>Name</th>
                    <th>Vehicle Type</th>
                    <th class="text-center">Edit</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($transfers as $tr)

                    <tr>
                        <td>{{ $tr->id }}</td>
                        <td class="text-center">
                            @if($tr->primary_image)
                                <div class="ds-avatar">
                                    <div class="ds-mask ds-mask-squircle w-16 h-16">
                                        <img src="{{$tr->primary_image?->getFullUrl()}}" />
                                    </div>
                                </div>
                            @else
                                <x-icon name="photograph" class="opacity-50 text-gray-400"/>
                            @endif

                        </td>
                        <td >{{ $tr->name }}</td>
                        <td>{{$tr->vehicle->type}}</td>
                        <td class="text-center">
                            <x-button.circle icon="pencil" primary  href="{{ route('transfer-edit',$tr)}}">
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
                                    <label>No defined transfers</label>
                                </div>
                            </div>
                        </TD>
                    </tr>
                @endforelse
                </tbody>

            </table>
            {{$transfers->links()}}


            <x-modal.card title="{{  !empty($this->transfer->exists) ? 'Updating':'Adding' }}" wire:model="transferModal">

                <x-input wire:model="transferName" label="Name:"></x-input>

                <x-native-select
                                 wire:model="vehicleId"
                                 label="Vehicle:"
                                 option-key-value
                                 :options="$this->vehicles->pluck('type','id')"
                />

                <x-slot name="footer" >
                    <div class="flex justify-between">

                    <x-button wire:click="closeTransferModal()" >Close</x-button>
                    <x-button wire:click="saveTransferData()"
                           positive>{{  !empty($this->transfer->exists) ? 'Update':'Add' }}</x-button>
                    </div>
                </x-slot>
            </x-modal.card>




    </x-card>


</div>
