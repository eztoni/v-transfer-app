<div>
    <x-ez-card>
        <x-slot name="title" class="flex justify-between">
            Transfers

            <button wire:click="addTransfer" class="btn btn-sm ">Add Transfer</button>

        </x-slot>
        <x-slot name="body">

            <input type="text" wire:model="search" class="input input-primary my-2" placeholder="Find Transfer">

            <table class="table table-compact w-full" wire:loading.delay.class="opacity-50">
                <thead>
                <tr>
                    <th>#Id</th>
                    <th>Name</th>
                    <th>Destination</th>
                    <th>Vehicle Type</th>
                    <th class="text-center">Edit</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($transfers as $tr)

                    <tr>
                        <td>{{ $tr->id }}</td>
                        <td >{{ $tr->name }}</td>
                        <td >{{ $tr->destination->name }}</td>
                        <td>{{$tr->vehicle->name}}</td>
                        <td class="text-center">
                            <a href="{{ route('transfer-edit',$tr) }}"><button class="btn btn-circle btn-sm btn-success">     <i class="fas fa-pen"></i></button></a>
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
                                    <label>No defined transfers</label>
                                </div>
                            </div>
                        </TD>
                    </tr>
                @endforelse
                </tbody>

            </table>
            {{$transfers->links()}}


            <div class="modal {{ $transferModal ? 'modal-open fadeIn' : '' }}">
                <div class="modal-box max-h-screen overflow-y-auto">
                    <b>{{  !empty($this->transfer->exists) ? 'Updating':'Adding' }} transfer</b>
                    <hr class="my-4">

                    <div class="form-control">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Name:</span>
                            </label>
                            <input wire:model="transferName" class="input input-bordered"
                                   placeholder="Name">
                            @error('transferName')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>

                    </div>

                    <div class="form-control">

                        <label class="label">
                            <span class="label-text">Vehicle:</span>
                        </label>
                        <select wire:model="vehicleId" class="select select-bordered">
                            <option value="">Select a vehicle</option>
                            @if($this->vehicles->isNotEmpty())
                                @foreach($this->vehicles as $ve)
                                    <option value="{{$ve->id}}">{{$ve->name}}</option>
                                @endforeach
                            @endif

                        </select>

                        @error('vehicleId')
                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                        @enderror
                    </div>


                    <div class="mt-4 flex justify-between">
                        <button wire:click="closeTransferModal()" class="btn btn-sm ">Close</button>
                        <button wire:click="saveTransferData()"
                                class="btn btn-sm ">{{  !empty($this->transfer->exists) ? 'Update':'Add' }}</button>
                    </div>
                </div>
            </div>

        </x-slot>

    </x-ez-card>

</div>

