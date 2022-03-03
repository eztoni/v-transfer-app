<div>

    <x-ez-card>
        <x-slot name="title" class="flex justify-between">
            Partners

            <button wire:click="addPartner" class="btn btn-sm ">Add Partner</button>

        </x-slot>
        <x-slot name="body">

            <input type="text" wire:model="search" class="input input-primary my-2" placeholder="Find Partner">
            <table class="table table-compact w-full" wire:loading.delay.class="opacity-50">
                <thead>
                <tr>
                    <th>#Id</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th class="text-center">Update</th>
                    <th class="text-right"><span class="pr-4">Delete</span></th>

                </tr>
                </thead>
                <tbody>
                @forelse ($partners as $p)

                    <tr>
                        <th>{{ $p->id }}</th>
                        <th >{{ $p->name }}</th>
                        <th >{{ $p->contact }}</th>
                        <th >{{ $p->email }}</th>
                        <td class="text-center">
                            <button wire:click="updatePartner({{$p->id}})" class="btn btn-sm btn-success">
                                Update
                            </button>
                        </td>
                        <td class="text-right">
                            <button wire:click="openSoftDeleteModal({{$p->id}})" class="btn btn-sm btn-ghost">
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
                                    <label>No defined partners</label>
                                </div>
                            </div>
                        </TD>
                    </tr>
                @endforelse
                </tbody>


            </table>
            {{$partners->links()}}


            <div class="modal {{ $softDeleteModal ? 'modal-open fadeIn' : '' }}">
                <div class="modal-box max-h-screen overflow-y-auto">
                    <b>Confirm deletion?</b>
                    <p>This action will delete the company.</p>
                    <hr class="my-4">

                    <div class="mt-4 flex justify-between">
                        <button wire:click="closeSoftDeleteModal()" class="btn btn-sm ">Close</button>
                        <button wire:click="softDelete()" class="btn btn-sm ">Delete</button>
                    </div>
                </div>
            </div>


            <div class="modal {{ $partnerModal ? 'modal-open fadeIn' : '' }}">
                <div class="modal-box max-h-screen overflow-y-auto">
                    New partner
                    <hr class="my-4">

                    <div class="form-control">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Name :</span>
                            </label>
                            <input wire:model="partner.name" class="input input-bordered"
                                   placeholder="Name">
                            @error('partner.name')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>
                    </div>

                    <div class="form-control">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Contact :</span>
                            </label>
                            <input wire:model="partner.contact" class="input input-bordered"
                                   placeholder="Contact">
                            @error('partner.contact')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>
                    </div>

                    <div class="form-control">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Email :</span>
                            </label>
                            <input wire:model="partner.email" class="input input-bordered"
                                   placeholder="Email">
                            @error('partner.email')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>
                    </div>


                    <div class="form-control">

                        <label class="label">
                            <span class="label-text">Destination:</span>
                        </label>
                        <select wire:model="partner.destination_id" class="select select-bordered">
                            <option value="">Select a destination</option>
                            @foreach($destinations as $destination)
                                <option value="{{$destination->id}}">{{$destination->name}}</option>
                            @endforeach
                        </select>

                        @error('partner.destination_id')
                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                        @enderror
                    </div>

                    <div class="mt-4 flex justify-between">
                        <button wire:click="closePartnerModal()" class="btn btn-sm ">Close</button>
                        <button wire:click="savePartnerData()"
                                class="btn btn-sm ">{{  !empty($this->partner->exists) ? 'Update':'Add' }}</button>
                    </div>
                </div>
            </div>

        </x-slot>

    </x-ez-card>
</div>

