<div>

    <x-ez-card>
        <x-slot name="title" class="flex justify-between">
            Routes

            <button wire:click="addModel" class="btn btn-sm ">Add {{$modelName}}</button>

        </x-slot>
        <x-slot name="body">


            <table class="table table-compact w-full" wire:loading.delay.class="opacity-50">
                <thead>
                <tr>
                    @foreach($this->setTableColumns() as $column => $text)
                        <th>{{$text}}</th>
                    @endforeach
                    <th class="text-center">Update</th>
                    <th class="text-right"><span class="pr-4">Delete</span></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($this->models as $m)

                    <tr>
                        @foreach($this->setTableColumns() as $column => $text)
                            <th>{{ $m->$column }}</th>
                        @endforeach

                        <td class="text-center">
                            <button wire:click="updateModel({{$m->id}})" class="btn btn-sm btn-success">
                                Update
                            </button>
                        </td>
                        <td class="text-right">
                            <button wire:click="openSoftDeleteModal({{$m->id}})" class="btn btn-sm btn-ghost">
                                Delete
                            </button>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="999">
                            <div class="alert alert-warning">
                                <div class="flex-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         class="w-6 h-6 mx-2 stroke-current">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <label>No defined {{$pluralModelName}}</label>
                                </div>
                            </div>
                        </TD>
                    </tr>
                @endforelse
                </tbody>


            </table>
            {{$models->links()}}


            <div class="modal {{ $softDeleteModal ? 'modal-open fadeIn' : '' }}">
                <div class="modal-box max-h-screen overflow-y-auto">
                    <b>Confirm deletion?</b>
                    <p>This action will delete the {{$modelName}}.</p>
                    <hr class="my-4">

                    <div class="mt-4 flex justify-between">
                        <button wire:click="closeSoftDeleteModal()" class="btn btn-sm ">Close</button>
                        <button wire:click="softDelete()" class="btn btn-sm ">Delete</button>
                    </div>
                </div>
            </div>




        </x-slot>

    </x-ez-card>
</div>

