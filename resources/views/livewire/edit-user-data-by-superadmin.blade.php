<div>
    <x-ez-card>
        <x-slot name="body">

            <label class="label">
                <span class="label-text">Name:</span>
            </label>
            <input wire:model.debounce.500ms="editData.name" class="input input-bordered" placeholder="Name">
            @error('name')<x-input-alert type='editData.info'>{{ $message }}</x-input-alert>@enderror

            <label class="label">
                <span class="label-text">Email:</span>
            </label>
            <input wire:model.debounce.500ms="editData.email" class="input input-bordered" placeholder="Email">
            @error('email')<x-input-alert type='warning'>{{ $message }}</x-input-alert>@enderror

            <label class="label">
                <span class="label-text">City:</span>
            </label>
            <input wire:model.debounce.500ms="editData.city" class="input input-bordered" placeholder="City">
            @error('city') <span class="bg-error">{{ $message }}</span>@enderror

            <label class="label">
                <span class="label-text">Zip:</span>
            </label>
            <input wire:model.debounce.500ms="editData.zip" class="input input-bordered" placeholder="Zip">
            @error('zip') <span class="bg-error">{{ $message }}</span>@enderror

            <label class="label">
                <span class="label-text">Country Code:</span>
            </label>
            <input wire:model="editData.country_code" class="input input-bordered" placeholder="Country Code">
            @error('country_code') <span class="bg-error">{{ $message }}</span>@enderror

            <div class="mt-5 ml-auto">
                <button wire:click.prevent="saveUserData()" class="btn btn-primary">Submit</button>
            </div>



        </x-slot>
    </x-ez-card>

</div>
