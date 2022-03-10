<div>
    <x-ez-card>
        <x-slot name="title" class="flex justify-between">
            Extras

            <button wire:click="addExtra" class="btn btn-sm ">Add Extra</button>

        </x-slot>
        <x-slot name="body">

            <input type="text" wire:model="search" class="input input-primary my-2" placeholder="Find Extra">
            <table class="table table-compact w-full" wire:loading.delay.class="opacity-50">
                <thead>
                <tr>
                    <th>#Id</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th class="text-center">Update</th>
                    <th class="text-center">Edit Extra</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($extras as $ex)

                    <tr>
                        <th>{{ $ex->id }}</th>
                        <th >{{ $ex->name }}</th>
                        <th >{{ $ex->price }}</th>
                        <td class="text-center">
                            <button wire:click="updateExtra({{$ex->id}})" class="btn btn-sm btn-warning">
                                Update
                            </button>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('extras-edit',$ex) }}"><button class="btn btn-sm btn-success">Images</button></a>
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
                                    <label>No defined extras</label>
                                </div>
                            </div>
                        </TD>
                    </tr>
                @endforelse
                </tbody>

            </table>
            {{$extras->links()}}


            <div class="modal {{ $extraModal ? 'modal-open fadeIn' : '' }}">
                <div class="modal-box max-h-screen overflow-y-auto">
                    <b>{{  !empty($this->extra->exists) ? 'Updating':'Adding' }} extra</b>
                    <hr class="my-4">

                    <div class="form-control">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Name :</span>
                            </label>
                            <input wire:model="extra.name" class="input input-bordered"
                                   placeholder="Name">
                            @error('extra.name')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>

                    </div>

                    <div class="form-control">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Description : </span>
                            </label>
                            <input wire:model="extra.description" class="input input-bordered"
                                   placeholder="Description">
                            @error('extra.description')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>
                    </div>

                    <div class="form-control">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Price (EUR) : </span>
                            </label>
                            <input wire:model="price" class="input input-bordered"
                                   placeholder="Price">
                            @error('price')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 flex justify-between">
                        <button wire:click="closeExtraModal()" class="btn btn-sm ">Close</button>
                        <button wire:click="saveExtraData()"
                                class="btn btn-sm ">{{  !empty($this->extra->exists) ? 'Update':'Add' }}</button>
                    </div>
                </div>
            </div>

        </x-slot>

    </x-ez-card>

</div>

