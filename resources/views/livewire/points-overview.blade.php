<x-card title="{{$destination->name}} - Pickup & Dropoff Points">
    <x-slot name="action" >
        <x-button wire:click="addPoint" positive>Add Point</x-button>
    </x-slot>

    <table class="ds-table ds-table-compact w-full" wire:loading.delay.class="opacity-50">
        <thead>
        <tr>
            <th>#ID</th>
            <th>Name</th>
            <th class="text-center">Update</th>
            <th class="text-center">Delete</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($points as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->name }}</td>
                <td class="text-center">
                    <x-button.circle primary wire:click="updatePoint({{$p->id}})" icon="pencil">
                    </x-button.circle>
                </td>
                <td class="text-center">
                    <x-button
                        wire:click="openSoftDeleteModal({{$p->id}})"
                        rounded
                        rose icon="trash"
                        target="_blank"
                    />
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

            <x-input label="Name:" wire:model="point.name"/>
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

            @endif

            <x-input label="PMS code:" wire:model="point.pms_code"/>

            <x-slot name="footer">
                <div class="mt-4 flex justify-between">
                    <x-button wire:click="closePointModal()" >Close</x-button>
                    <x-button wire:click="savePointData()" positive
                    >{{  !empty($this->point->exists) ? 'Update':'Add' }}</x-button>
                </div>
            </x-slot>


        </div>

    </x-modal.card>

    <x-modal.card blur wire:model.defer="softDeleteModal" title="Delete Point - {{$this->point->name}}">

        <p>You are about to delete <b>{{$this->point->name}}</b> are you sure you want to do that?</p>
        <p class="text-rose-500">CAREFUL - This action will delete the point!</p>

        <x-slot name="footer">
            <div class="float-right">
                <x-button wire:click="closeSoftDeleteModal()" label="Close" rose/>
                <x-button wire:click="softDelete()" label="Delete" positive/>
            </div>

        </x-slot>

    </x-modal.card>
    {{$points->links()}}

</x-card>

