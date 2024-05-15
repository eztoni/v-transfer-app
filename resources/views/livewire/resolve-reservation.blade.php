<div class=" my-4">
    <x-input label="Add A Comment:" wire:model="comment"></x-input>
    <small>Comment not mandatory</small>
    <div class="ds-divider"></div>
    <div class="flex justify-end">
        <x-button positive label="Mark As Resolved"  wire:click="resolveReservation" />
        <x-button label="Cancel"  negative wire:click="close"/>
    </div>
</div>
