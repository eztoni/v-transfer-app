<div x-data="ezCrud()">

    <x-ez-card>
        <x-slot name="title" class="flex justify-between">
            Routes

            <button wire:click="addModel" class="btn btn-primary btn-sm "><i class="fas fa-plus mr-2"></i>Add {{$modelName}}</button>

        </x-slot>
        <x-slot name="body">


            <table class="table table-compact w-full" wire:loading.delay.class="opacity-50">
                <thead>
                <tr>
                    @foreach($this->tableColumns() as $column => $text)
                        <th>{{$text}}</th>
                    @endforeach
                    <th class="text-center">Update</th>
                        @if($this->enableDelete)

                        <th class="text-center"><span>Delete</span></th>
                            @endif
                </tr>
                </thead>
                <tbody>
                @forelse ($models as $m)

                    <tr>
                        @foreach($this->tableColumns() as $column => $text)
                            <td>{{ $this->getModelPropertyBasedOnDottedString($m,$column) }}</td>
                        @endforeach

                        <td class="text-center">
                            <button wire:click="updateModel({{$m->id}})" class="btn btn-circle btn-sm btn-success">
                                <i class="fas fa-pen"></i>
                            </button>
                        </td>
                            @if($this->enableDelete)
                                <td class="text-center">
                                    <button wire:click="openSoftDeleteModal({{$m->id}})"
                                            class="btn btn-sm btn-circle btn-outline ">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            @endif

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
                        <button wire:click="closeSoftDeleteModal()" class="btn  ">Close</button>
                        <button wire:click="softDelete()" class="btn btn-success ">
                            <i class="fas fa-trash mr-2"></i>
                            Delete</button>
                    </div>
                </div>
            </div>


            <div class="modal {{ $crudModal ? 'modal-open fadeIn' : '' }}">
                <div class="modal-box max-h-screen overflow-y-auto">
                    {{  !empty($this->model->exists) ? 'Update':'Add new' }} {{$modelName}}
                    <hr class="my-4">
                    @if($model)
                        @foreach($this->formFields() as $field)
                           {{$field->render()->with($field->data())}}
                        @endforeach
                    @endif

                    <div class="mt-4 flex justify-between">
                        <button wire:click="closeCrudModal()" class="btn  ">Close</button>
                        <button wire:click="saveModelData()"
                                class="btn  btn-success">
                            {{  !empty($this->model->exists) ? 'Update':'Add' }}
                            <i class="fas scale-110 fa-save ml-2"></i>

                        </button>
                    </div>
                </div>
            </div>

        </x-slot>

    </x-ez-card>
</div>

<script>
    function ezCrud(){
        return {

        }
    }
</script>
