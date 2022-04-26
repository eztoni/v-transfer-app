<div>

    <x-ez-card class="mb-4">

        <x-slot name="title" class="flex justify-between">
            <span><i class="fas fa-book"></i> Booking overview:</span>
        </x-slot>

        <x-slot name="body">

            <div class="flex md:flex-row flex-col gap-4">


                <div class="flex md:flex-row flex-col flex-warp flex-grow gap-4">
                    <input class="my-input input-sm" placeholder="Booking number">
                    <input wire:ignore="" placeholder="Date" wire:model.debounce.300ms="dateRange" class="input input-sm input-bordered flatpickr-input" x-init="flatpickr($el, {disableMobile: 'true',mode: 'range' });" readonly="readonly" type="text">
                    <button class="btn btn-sm btn-primary">Search</button>
                </div>

                <button class="btn btn-sm btn-success" wire:click="saveTransfer">+ Add Booking </button>


            </div>



        </x-slot>
    </x-ez-card>



</div>
