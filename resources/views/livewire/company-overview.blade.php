<div x-data="app()">

    <x-ez-card>
        <x-slot name="title" class="flex justify-between">
            Companies

            <button wire:click="addCompany" class="btn btn-sm ">Add Company</button>

        </x-slot>
        <x-slot name="body">

            <input type="text" wire:model="search" class="input input-primary my-2" placeholder="Find Company">
            <table class="table table-compact w-full" wire:loading.delay.class="opacity-50">
                <thead>
                <tr>
                    <th>#Id</th>
                    <th>Name</th>
                    <th class="text-center">Update</th>
                    <th class="text-right"><span class="pr-4">Delete</span></th>

                </tr>
                </thead>
                <tbody>
                @forelse ($companies as $company)

                    <tr>
                        <th>{{ $company->id }}</th>
                        <th >{{ $company->name }}</th>
                        <td class="text-center">
                            <button wire:click="updateCompany({{$company->id}})" class="btn btn-sm btn-success">
                                Update
                            </button>
                        </td>
                        <td class="text-right">
                            <button wire:click="openSoftDeleteModal({{$company->id}})" class="btn btn-sm btn-ghost">
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
                                    <label>No defined companies</label>
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
                    <p>This action will delete the company.</p>
                    <hr class="my-4">

                    <div class="mt-4 flex justify-between">
                        <button wire:click="closeSoftDeleteModal()" class="btn btn-sm ">Close</button>
                        <button wire:click="softDelete()" class="btn btn-sm ">Delete</button>
                    </div>
                </div>
            </div>


            <div class="modal {{ $companyModal ? 'modal-open fadeIn' : '' }}">
                <div class="modal-box max-h-screen overflow-y-auto">
                    Adding new company
                    <hr class="my-4">

                    <div class="form-control">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Name :</span>
                            </label>
                            <input wire:model="company.name" class="input input-bordered"
                                   placeholder="Name">
                            @error('company.name')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>

                        <div class="form-control">

                            <label class="label">
                                <span class="label-text">Country:</span>
                            </label>
                            <x-country-select class="w-full" livewireModel="company.country_id"></x-country-select>
                            @error('company.country_id')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Email:</span>
                            </label>
                            <input wire:model="company.email" class="input input-bordered"
                                   placeholder="email">
                             @error('company.email')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Contact:</span>
                            </label>
                            <input wire:model="company.contact" class="input input-bordered"
                                   placeholder="Contact">
                              @error('company.contact')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Zip :</span>
                            </label>
                            <input wire:model="company.zip" class="input input-bordered"
                                   placeholder="Zip">
                              @error('company.zip')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">City:</span>
                            </label>
                            <input wire:model="company.city" class="input input-bordered"
                                   placeholder="City">
                             @error('company.city')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>


                    </div>

                    <div class="mt-4 flex justify-between">
                        <button wire:click="closeCompanyModal()" class="btn btn-sm ">Close</button>
                        <button wire:click="saveCompanyData()"
                                class="btn btn-sm ">{{  !empty($this->company->exists) ? 'Update':'Add' }}</button>
                    </div>
                </div>
            </div>

        </x-slot>

    </x-ez-card>
</div>

