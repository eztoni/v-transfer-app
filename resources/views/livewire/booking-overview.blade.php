<div>

    <x-ez-card class="mb-4">

        <x-slot name="title" class="flex justify-between">
            <span><i class="fas fa-book"></i> Booking overview:</span>
        </x-slot>

        <x-slot name="body">
            <div class="flex md:flex-row flex-col gap-4">
                <div class="flex md:flex-row flex-col flex-warp flex-grow gap-4">
                    <input class="my-input input-sm" placeholder="Booking number">
                    <select wire:model="destinationId"  class="my-input select-sm">
                        <option>Destination</option>
                        @foreach($destinations as $destination)
                            <option value="{{$destination->id}}">{{$destination->name}}</option>
                        @endforeach
                    </select>
                    <select wire:model="partnerId" class="my-input select-sm">
                        <option>Select a partner</option>
                        @foreach($partners as $partner)
                            <option value="{{$partner->id}}">{{$partner->name}}</option>
                        @endforeach
                    </select>
                    <input wire:ignore="" placeholder="Date" wire:model.debounce.300ms="dateRange" class="input input-sm input-bordered flatpickr-input" x-init="flatpickr($el, {disableMobile: 'true',mode: 'range' });" readonly="readonly" type="text">
                    <button class="btn btn-sm btn-primary">Search</button>
                </div>

                <a href="{{route('internal-reservation')}}"><button class="btn btn-sm btn-success">+ Add Booking </button></a>
            </div>
        </x-slot>
    </x-ez-card>

    @foreach($this->reservations as $reservation)

        <x-ez-card class="mb-4">

            <x-slot name="title" class="flex justify-between">
                <div>
                    <span>Transfer #{{$reservation->id}}</span>
                    <span class="badge badge-success">One Way</span>
                </div>

                <a href="{{route('reservation-details',$reservation->id)}}"><button class="btn btn-sm btn-primary">View</button></a>
            </x-slot>

            <x-slot name="body">
                <div class="flex flex-col w-full">
                    <div class="flex gap-4 md:flex-row flex-col basis-2/3">
                        <span class="font-extrabold text-info">Lead:</span>
                        <span><i class="text-xs fas fa-user"></i> {{$reservation->leadTraveller?->full_name}}  {{$reservation->leadTraveller?->email}}</span>
                        <span><i class="text-xs fas fa-phone"></i> {{$reservation->leadTraveller?->phone}}</span>
                    </div>
                    <div class="m-0 divider"></div>
                    <div class="flex gap-4 md:flex-row flex-col basis-2/3">
                        <span class="font-extrabold text-info">Passengers: </span> <b>{{$reservation->num_passangers}}</b>
                        <span class="font-extrabold text-info">Luggage:</span> <b>{{$reservation->luggage}}</b>

                        <span class="font-extrabold text-info">Pickup Location:</span>
                        <span><b>{{$reservation->pickupLocation->name}} - <b>{{$reservation->date}}</b> @ <b>{{$reservation->time}}</b> - <b>Address: {{$reservation->pickup_address}}</b></b></span>
                    </div>
                </div>
            </x-slot>
        </x-ez-card>


    @endforeach


</div>
