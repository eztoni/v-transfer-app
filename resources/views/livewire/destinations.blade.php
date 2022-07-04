<div >

    <x-card title="Destinations">
        <x-slot name="action" >

            <x-button wire:click="addDestination" positive>Add Destination</x-button>

        </x-slot>

            <x-input type="text" wire:model="search" placeholder="Find Destination"/>
            <table class="ds-table ds-table-compact w-full" wire:loading.delay.class="opacity-50">
                <thead>
                <tr>
                    <th>#Id</th>
                    <th>Name</th>
                    <th class="text-center">Update</th>


                </tr>
                </thead>
                <tbody>
                @forelse ($destinations as $d)


                    <tr>
                        <td>{{ $d->id }}</td>
                        <td>{{ $d->name }}</td>
                        <td class="text-center">
                            <x-button.circle icon="pencil" wire:click="updateDestination({{$d->id}})" primary>
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
                                    <label>No defined destinations</label>
                                </div>
                            </div>
                        </TD>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {{ $destinations->links() }}


        <x-modal.card wire:model="softDeleteModal" title="Confirm deletion?">
            <p>This action will delete the destination.</p>
            <hr class="my-4">

            <x-slot name="footer" >
                <div class="mt-4 flex justify-between">
                    <x-button wire:click="closeSoftDeleteModal()" >Close</x-button>
                    <x-button wire:click="softDelete()" positive>Delete</x-button>
                </div>

            </x-slot>
        </x-modal.card>


        <x-modal.card wire:model="destinationModal"
                      title="{{  !empty($this->destination->exists) ? 'Update':'Add' }} new destination"
        >
            <x-input label="Name:" wire:model="destination.name"></x-input>

            <x-slot name="footer" >
                <div class="flex justify-between">

                <x-button wire:click="closeDestinationModal()" >Close</x-button>
                <x-button wire:click="saveDestinationData()" positive
                        class="btn btn-sm ">{{  !empty($this->destination->exists) ? 'Update':'Add' }}</x-button>
                </div>
            </x-slot>
        </x-modal.card>




    </x-card>
</div>

