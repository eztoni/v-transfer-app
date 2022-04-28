<div>

    <x-ez-card class="mb-4">

        <x-slot name="title" class="flex justify-between">
            <span><i class="fas fa-book"></i> Booking overview:</span>
        </x-slot>

        <x-slot name="body">
            <div class="flex md:flex-row flex-col gap-4">
                <div class="flex md:flex-row flex-col flex-warp flex-grow gap-4">
                    <input class="my-input input-sm" placeholder="Booking number">
                    <select class="my-input select-sm">
                        <option>Select a partner</option>
                    </select>
                    <input wire:ignore="" placeholder="Date" wire:model.debounce.300ms="dateRange" class="input input-sm input-bordered flatpickr-input" x-init="flatpickr($el, {disableMobile: 'true',mode: 'range' });" readonly="readonly" type="text">
                    <button class="btn btn-sm btn-primary">Search</button>
                </div>

                <a href="{{route('internal-reservation')}}"><button class="btn btn-sm btn-success" wire:click="saveTransfer">+ Add Booking </button></a>
            </div>
        </x-slot>
    </x-ez-card>


    <x-ez-card class="mb-4">

        <x-slot name="title" class="flex justify-between">
            <div>
                <span>Transfer #230413823</span>
                <span class="badge badge-success">One Way</span>
            </div>

            <b href="{{route('reservation-view')}}"><button class="btn btn-sm btn-primary" wire:click="saveTransfer">View </button></b>
        </x-slot>

        <x-slot name="body">
            <div class="flex flex-col w-full">
                <div class="flex gap-4 md:flex-row flex-col basis-2/3">
                    <span class="font-extrabold text-info">Lead:</span>
                    <span><i class="text-xs fas fa-user"></i> Mr. Sam Rosterula (samdfed@test.com)</span>
                    <span><i class="text-xs fas fa-phone"></i> +483 032 5342 42</span>
                </div>
                <div class="m-0 divider"></div>
                <div class="flex gap-4 md:flex-row flex-col basis-2/3">
                    <span class="font-extrabold text-info">Passengers: </span> <b>4</b>
                    <span class="font-extrabold text-info">Luggage:</span> <b>8</b>

                    <span class="font-extrabold text-info">Pickup:</span>
                    <span><b>Zadar - <b>25.5.2022</b> @ <b>12:45</b></b></span>
                </div>
            </div>
        </x-slot>
    </x-ez-card>

    <x-ez-card class="mb-4">

        <x-slot name="title" class="flex justify-between">
            <div>
                <span>Transfer #54353413823</span>
                <span class="badge badge-success">Two Way</span>
            </div>

            <b href="{{route('reservation-view')}}"><button class="btn btn-sm btn-primary" wire:click="saveTransfer">View </button></b>
        </x-slot>

        <x-slot name="body">
            <div class="flex flex-col w-full">
                <div class="flex gap-4 md:flex-row flex-col basis-2/3">
                    <span class="font-extrabold text-info">Lead:</span>
                    <span><i class="text-xs fas fa-user"></i> Mr. Dave Davidson (samtvahf@test.com)</span>
                    <span><i class="text-xs fas fa-phone"></i> +483 032 5342 42</span>
                </div>
                <div class="m-0 divider"></div>
                <div class="flex gap-4 md:flex-row flex-col basis-2/3">
                    <span class="font-extrabold text-info">Passengers: </span> <b>4</b>
                    <span class="font-extrabold text-info">Luggage:</span> <b>8</b>

                    <span class="font-extrabold text-info">Pickup:</span>
                    <span><b>Zadar - <b>25.5.2022</b> @ <b>12:45</b></b></span>

                    <span class="font-extrabold text-info">Dropoff:</span>
                    <span><b>Šibenik - <b>25.5.2022</b> @ <b>12:45</b></b></span>

                </div>
            </div>
        </x-slot>
    </x-ez-card>



</div>
