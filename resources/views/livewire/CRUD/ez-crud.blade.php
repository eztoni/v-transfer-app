    <x-card
        :title="$pluralModelName"
    >
        <x-slot name="action">
            <x-button wire:click="addModel" positive icon="plus">Add {{$modelName}}</x-button>
        </x-slot>
        <table class="ds-table ds-table-compact w-full" wire:loading.delay.class="opacity-50">
            <thead>
            <tr>
                @foreach($this->tableColumns() as $text => $column )
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
                    @foreach($this->tableColumns() as  $text => $column)
                        @if(method_exists($this,$column))
                            <td>  {!!   $this->{$column}($m) !!}</td>
                        @else
                            <td>{{ $this->getModelPropertyBasedOnDottedString($m,$column) }}</td>
                        @endif
                    @endforeach

                    <td class="text-center">
                        <x-button.circle icon="pencil" primary wire:click="updateModel({{$m->id}})">
                        </x-button.circle>
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
                        <div class="ds-alert ds-alert-warning">
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
        <x-modal.card wire:model="crudModal"
                      title="{{  !empty($this->model->exists) ? 'Update':'Add new' }} {{$modelName}}">

            <x-dynamic-component :component="'crud.forms.'.$this->formBladeViewName()"/>
            <x-slot name="footer">
                <div class="float-right">
                    <x-button wire:click="closeCrudModal()">Close</x-button>
                    <x-button wire:click="saveModelData()"
                              positive>
                        {{  !empty($this->model->exists) ? 'Update':'Add' }}
                    </x-button>
                </div>
            </x-slot>


        </x-modal.card>

    </x-card>


