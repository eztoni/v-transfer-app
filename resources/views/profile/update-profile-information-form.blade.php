<x-jet-form-section submit="updateProfileInformation">
    <x-slot name="title" >
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-input
                label="Name"
                wire:model.defer="state.name"
                autocomplete="name"
                id="name"
                type="text"
            />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-input
                id="email" type="email"
                wire:model.defer="state.email"
                label="Email"
            />
        </div>
        <div class="col-span-6">
        <x-errors></x-errors>

        </div>

    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>

        <x-button
            type="submit"
        >
            {{ __('Save') }}
        </x-button>


    </x-slot>
</x-jet-form-section>
