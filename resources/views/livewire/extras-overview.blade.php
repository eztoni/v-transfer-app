<div>
    <x-card title="Extras">
        <x-slot name="action" >
            <x-button wire:click="addExtra" positive>Add Extra</x-button>
        </x-slot>


            <x-input  wire:model="search" class="my-2"  placeholder="Find Extra"/>
            <table class="ds-table ds-table-compact w-full" wire:loading.delay.class="opacity-50">
                <thead>
                <tr>
                    <th>#Id</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th class="text-center">Edit</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($extras as $ex)

                    <tr>
                        <th>{{ $ex->id }}</th>
                        <th>{{ $ex->name }}</th>
                        <th>{{ strlen($ex->description) > 25 ? substr($ex->description,0,25)."..." : $ex->description }}</th>
                        <td class="text-center">
                            <x-button.circle icon="pencil" primary href="{{ route('extras-edit',$ex)}}">
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
                                    <label>No defined extras</label>
                                </div>
                            </div>
                        </TD>
                    </tr>
                @endforelse
                </tbody>


            </table>
            {{$extras->links()}}


        <x-modal.card wire:model="extraModal" title="{{$extraModal ? 'modal-open fadeIn' : ''}}">

            <x-input label="Name:" wire:model="extraName.en" placeholder="ex. Drinks"></x-input>
            <x-input label="Description:" wire:model="extraDescription.en"></x-input>





            <x-slot name="footer" >
                <div class="flex justify-between">
                <x-button wire:click="closeExtraModal()" >Close</x-button>
                <x-button wire:click="saveExtraData()" positive
                       >{{  !empty($this->extra->exists) ? 'Update':'Add' }} </x-button>
                </div>
            </x-slot>
        </x-modal.card>
    </x-card>
</div>


