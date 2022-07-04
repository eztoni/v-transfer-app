<div>

    <x-ez-card>
        <x-slot name="title" class="flex justify-between">
            Age group categories - {{$ageGroup->name}}

            <div>
                <button wire:click="addAgeCategory" class="btn btn-sm ">Add Age Category</button>
                <a href="{{ route('age-groups') }}"><button class="btn btn-sm btn-primary">Back</button></a>
            </div>

        </x-slot>
        <x-slot name="body">

            <table class="ds-table ds-table-compact w-full" wire:loading.delay.class="opacity-50">
                <thead>
                <tr>
                    <th>Age Category</th>
                    <th>Age from</th>
                    <th>Age to</th>
                    <th class="text-center">Update</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($ageCategories as $ageCategory)

                    <tr>
                        <th>{{ ucfirst($ageCategory->category_name) }}</th>
                        <th >{{ $ageCategory->age_from }}</th>
                        <th >{{ $ageCategory->age_to }}</th>
                        <td class="text-center">
                            <button wire:click="updateAgeCategory({{$ageCategory->id}})" class="btn btn-sm btn-success">
                                Update
                            </button>
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
                                    <label>No defined age groups</label>
                                </div>
                            </div>
                        </TD>
                    </tr>
                @endforelse
                </tbody>

            </table>


            <div class="modal {{ $ageCategoryModal ? 'modal-open fadeIn' : '' }}">
                <div class="modal-box max-h-screen overflow-y-auto">
                    Adding new age group
                    <hr class="my-4">

                    <div class="form-control">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Age Category:</span>
                            </label>

                            <select  wire:model="ageCategory.category_name" class="select select-bordered">
                                <option  value="{{ null }}">Select Age Group</option>
                                    @foreach($this->availableAgeCategories as $age_category)
                                            <option value="{{$age_category}}">{{Str::ucfirst($age_category)}}</option>
                                    @endforeach
                            </select>
                            @error('ageCategory.category_name') <x-input-alert type='warning'>{{ $message }}</x-input-alert>@enderror
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Age from :</span>
                            </label>
                            <input wire:model="ageCategory.age_from" class="input input-bordered"
                                   placeholder="Age From">
                            @error('ageCategory.age_from')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Age to :</span>
                            </label>
                            <input wire:model="ageCategory.age_to" class="input input-bordered"
                                   placeholder="Age To">
                            @error('ageCategory.age_to')
                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                            @enderror
                        </div>
                        @error('ageOverlapError')
                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                        @enderror
                    </div>

                    <div class="mt-4 flex justify-between">
                        <button wire:click="closeAgeCategoryModal()" class="btn btn-sm ">Close</button>
                        <button wire:click="saveAgeCategoryData()"
                                class="btn btn-sm ">{{  !empty($this->ageCategory->exists) ? 'Update':'Add' }}</button>
                    </div>
                </div>
            </div>

        </x-slot>

    </x-ez-card>
</div>

