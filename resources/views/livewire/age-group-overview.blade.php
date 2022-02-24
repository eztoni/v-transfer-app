<div>
    <x-ez-card>
        <x-slot name="title" class="flex justify-between">
            Age Groups

            <button wire:click="addAgeGroup" class="btn btn-sm ">Add Age Group</button>

        </x-slot>
        <x-slot name="body">

            <input type="text" wire:model="search" class="input input-primary my-2" placeholder="Find Age Group">
            <table class="table table-compact w-full" wire:loading.delay.class="opacity-50">
                <thead>
                <tr>
                    <th>#Id</th>
                    <th>Name</th>
                    <th>Age Categories</th>
                    <th class="text-center">Update</th>
                    <th class="text-center">Edit Categories</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($ageGroups as $ageGroup)

                    <tr>
                        <th>{{ $ageGroup->id }}</th>
                        <th >{{ $ageGroup->name }}</th>
                        <th>{{ Str::headline($ageGroup->categories->implode('category_name', ',')) }}</th>
                        <td class="text-center">
                            <button wire:click="updateAgeGroup({{$ageGroup->id}})" class="btn btn-sm btn-warning">
                                Update
                            </button>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('age-group-categories',$ageGroup) }}"><button class="btn btn-sm btn-success">Add Categories </button></a>
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
                                    <label>No defined age groups</label>
                                </div>
                            </div>
                        </TD>
                    </tr>
                @endforelse
                </tbody>

            </table>


            <div class="modal {{ $ageGroupModal ? 'modal-open fadeIn' : '' }}">
                <div class="modal-box max-h-screen overflow-y-auto">
                    <b>{{  !empty($this->ageGroup->exists) ? 'Updating':'Adding' }} age group</b>
                    <hr class="my-4">

                    <div class="form-control">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Age Group Name :</span>
                            </label>
                            <input wire:model="ageGroup.name" class="input input-bordered"
                                   placeholder="Name">
                            @error('ageGroup.name')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-4 flex justify-between">
                        <button wire:click="closeAgeGroupModal()" class="btn btn-sm ">Close</button>
                        <button wire:click="saveAgeGroupData()"
                                class="btn btn-sm ">{{  !empty($this->ageGroup->exists) ? 'Update':'Add' }}</button>
                    </div>
                </div>
            </div>

        </x-slot>

    </x-ez-card>
</div>

