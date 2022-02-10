<div x-data="app()">

    <x-ez-card>
        <x-slot name="title" class="flex justify-between">
            Destinations

            <button wire:click="openDestinationModal" class="btn btn-sm ">Add Destination</button>

        </x-slot>
        <x-slot name="body">

            <input type="text" wire:model="search" class="input input-primary my-2" placeholder="Find Destination">
            <table class="table table-compact w-full" wire:loading.delay.class="opacity-50">
                <thead>
                <tr>
                    <th>#Id</th>
                    <th>Name</th>
                    <th>Delete</th>

                </tr>
                </thead>
                <tbody>
                @forelse ($destinations as $destination)


                    <tr>
                        <th>{{ $destination->id }}</th>
                        <th>{{ $destination->name }}</th>
                        <td ><button wire:click="openSoftDeleteModal({{$destination->id}})" class="btn btn-sm btn-warning">Delete</button></td>
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
                                    <label>No defined destinations</label>
                                </div>
                            </div>
                        </TD>
                    </tr>
                @endforelse
                </tbody>

            </table>


            <div class="modal {{ $softDeleteModal ? 'modal-open fadeIn' : '' }}">
                <div class="modal-box max-h-screen overflow-y-auto">
                    <b>Confirm deletion?</b>
                    <p>This action will delete the destination.</p>
                    <hr class="my-4">

                    <div class="mt-4 flex justify-between">
                        <button wire:click="closeSoftDeleteModal()" class="btn btn-sm ">Close</button>
                        <button wire:click="softDelete()"  class="btn btn-sm ">Delete</button>
                    </div>
                </div>
            </div>


            <div class="modal {{ $destinationModal ? 'modal-open fadeIn' : '' }}">
                <div class="modal-box max-h-screen overflow-y-auto">
                    Adding new destination
                    <hr class="my-4">

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Destination name :</span>
                        </label>
                        <input wire:model.debounce.500ms="editData.name" class="input input-bordered"
                               placeholder="Destination name">
                        @error('editData.name')
                        <x-input-alert type='warning'>Destination name is required.</x-input-alert>@enderror
                    </div>

                    <div class="mt-4 flex justify-between">
                        <button wire:click="closeDestinationModal()" class="btn btn-sm ">Close</button>
                        <button wire:click="saveDestinationData()"  class="btn btn-sm ">Add</button>
                    </div>
                </div>
            </div>

        </x-slot>

    </x-ez-card>
</div>
<script>
    function app(){
        return {
            deleteId:'',
            showModal(id){
                this.deleteId=id
                this.$refs.modal.checked = true
            },
            closeModal(){
                this.$refs.modal.checked = false
            },
            deleteModel(){
                this.$wire.softDelete(this.deleteId)
                this.closeModal()
                this.deleteId=''
            },
        }
    }

</script>

