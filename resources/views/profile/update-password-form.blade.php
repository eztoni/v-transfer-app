<x-jet-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('Update Password') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-input
                label="Current password"
                id="current_password" type="password"
                wire:model.defer="state.current_password" autocomplete="current-password"
            > </x-input>

        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-input
                label="New password"
                id="password" type="password"
                wire:model.defer="state.password" autocomplete="new-password"
            > </x-input>

        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-input
                label="Confirm Password"
                id="password_confirmation" type="password"
                wire:model.defer="state.password_confirmation" autocomplete="new-password"
            > </x-input>

        </div>

        <div class="col-span-6">
            <x-errors></x-errors>
        </div>

    </x-slot>

    <x-slot name="actions">


        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>

        <x-button type="submit">
            Save
        </x-button>

    </x-slot>
</x-jet-form-section>
