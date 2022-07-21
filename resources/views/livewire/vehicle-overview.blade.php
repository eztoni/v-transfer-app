<div>
    <x-card title="Vehicles">
        <x-slot name="action" >
            <x-button wire:click="addVehicle" positive>Add Vehicle</x-button>
        </x-slot>

            <x-input  wire:model="search" placeholder="Find Vehicle" />
            <table class="ds-table ds-table-compact w-full" wire:loading.delay.class="opacity-50">
                <thead>
                <tr>
                    <th>#Id</th>
                    <th>Type</th>
                    <th class="text-center">Edit</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($vehicles as $ve)

                    <tr>
                        <td>{{ $ve->id }}</td>
                        <td >{{ $ve->type }}</td>

                        <td class="text-center">
                            <x-button.circle primary href="{{ route('vehicle-edit',$ve) }}" icon="pencil">
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
            {{$vehicles->links()}}


        <x-modal.card wire:model="vehicleModal" title="{{  !empty($this->vehicle->exists) ? 'Updating':'Adding' }} vehicle">
            <div class="">



                <x-input label="Type:" wire:model="vehicle.type"/>
                <x-input label="Max Occ:" wire:model="vehicle.max_occ"/>
                <x-input label="Max Luggage:" wire:model="vehicle.max_luggage"/>

                <x-slot name="footer">
                    <div class="mt-4 flex justify-between">
                        <x-button wire:click="closeVehicleModal()" >Close</x-button>
                        <x-button wire:click="saveVehicleData()" positive
                              >{{  !empty($this->vehicle->exists) ? 'Update':'Add' }}</x-button>
                    </div>
                </x-slot>


            </div>

        </x-modal.card>




    </x-card>


</div>
