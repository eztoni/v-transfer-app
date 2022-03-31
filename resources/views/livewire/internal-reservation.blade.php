<div class="internal-reservation">
    <x-ez-card class="mb-4">

        <x-slot name="title">
            Search
        </x-slot>

        <x-slot name="body" class="">
            <div class="flex justify-between gap-4">
                <div class="form-control flex-grow basis-1/3">
                    <select class="my-select" wire:model="stepOneFields.destinationId">
                        <option value="">Pick a destination</option>
                        @foreach(\App\Models\Destination::all() as $destination)
                            <option value="{{$destination->id}}">{{$destination->name}}</option>

                        @endforeach


                    </select>

                </div>

                <div class="form-control flex-grow basis-1/3">
                    @if(!empty($this->stepOneFields['destinationId']))
                        <select class="my-select" wire:model="stepOneFields.pickupPointId">
                            <option value="">Pickup location</option>

                        </select>
                    @endif


                </div>
                <div class="form-control flex-grow basis-1/3">
                    @if(!empty($this->stepOneFields['destinationId']))

                        <select class="my-select" wire:model="stepOneFields.dropOffPointId">
                            <option value="">Drop off location</option>

                        </select>
                    @endif

                </div>


            </div>
            <div class="divider my-1    "></div>
            <div class="flex justify-between gap-4">
                <div class="basis-2/5  ">
                    <div class="flex justify-between gap-4">
                        <div class="form-control flex-grow ">
                            <label class="label">
                                <span class="label-text">Date:</span>
                            </label>
                            <input class="my-input input-sm flex-grow" placeholder="" value="31.04.2022">

                        </div>
                        <div class="form-control flex-grow ">
                            <label class="label">
                                <span class="label-text">Time:</span>
                            </label>
                            <input class="my-input input-sm flex-grow" placeholder="" value="22:45">

                        </div>
                    </div>
                    <div class="form-control rounded ">
                        <label class="label cursor-pointer bg-gray-100 rounded mt-6 mb-1  ">
                            <span class="label-text">
                              <i class="fas fa-exchange-alt mx-4"></i>
                                 Two way</span>
                            <input type="checkbox" checked="checked" class="checkbox">
                        </label>
                        <div class="flex justify-between gap-4">
                            <div class="form-control flex-grow ">
                                <label class="label">
                                    <span class="label-text">Date (return):</span>
                                </label>
                                <input class="my-input input-sm flex-grow" placeholder="" value="31.04.2022">

                            </div>
                            <div class="form-control flex-grow ">
                                <label class="label">
                                    <span class="label-text">Time (return):</span>
                                </label>
                                <input class="my-input input-sm flex-grow" placeholder="" value="22:45">

                            </div>
                        </div>
                    </div>

                </div>


                <div class="divider divider-horizontal md:opacity-100 opacity-0"></div>

                <div class="basis-3/5">
                    <div class="flex justify-between gap-4">

                        <div class="form-control flex-grow ">
                            <label class="label">
                                <span class="label-text">Senior:</span>
                            </label>
                            <input class="my-input input-sm flex-grow" placeholder="" value="1">

                        </div>
                        <div class="form-control flex-grow ">
                            <label class="label">
                                <span class="label-text">Adult:</span>
                            </label>
                            <input class="my-input input-sm flex-grow" placeholder="" value="2">

                        </div>

                    </div>
                    <div class="flex justify-between gap-4">

                        <div class="form-control flex-grow ">
                            <label class="label">
                                <span class="label-text">Child(3-17):</span>
                            </label>
                            <input class="my-input input-sm flex-grow" placeholder="" value="1">

                        </div>
                        <div class="form-control flex-grow ">
                            <label class="label">
                                <span class="label-text">Infant(0-2):</span>
                            </label>
                            <input class="my-input input-sm flex-grow" placeholder="" value="1">

                        </div>
                    </div>
                    <div class="flex justify-between gap-4">

                        <div class="form-control flex-grow ">
                            <label class="label">
                                <span class="label-text">Luggage</span>
                            </label>
                            <input class="my-input input-sm flex-grow" placeholder="" value="1">

                        </div>

                    </div>

                </div>

            </div>
        </x-slot>
    </x-ez-card>

    <div class="grid grid-cols-3 gap-4">
        <div class="col-span-2 ">

            <x-ez-card>
                <x-slot name="title">Transfers</x-slot>
                <x-slot name="body">

                    <div class="card  bg-gray-200 ">
                        <div class="card-body p-2">

                            <div class="flex gap-4">
                                <div class="basis-1/5">


                                            <img class="h-24 w-full object-cover rounded-xl" src="https://api.lorem.space/image/face?hash=88560" />


                                </div>
                                <div class="basis-4/5">
                                    <h2 class="card-title">Private transfer - Van - Valamar official</h2>
                                    <div class="flex gap-4">
                                        <span class="badge ">Type: Van</span>
                                        <span class="badge ">Max. Occ: 8</span>
                                        <span class="badge ">Max. Luggage: 8</span>

                                    </div>
                                    <span class="badge badge-primary absolute right-8 top-4 ">Price: 342.25 EUR</span>

                                    <button class="btn btn-sm btn-primary absolute bottom-2  right-8">Select</button>
                                </div>

                            </div>


                        </div>
                    </div>



                </x-slot>
            </x-ez-card>

        </div>
        <div class="col-span-1">
                <x-ez-card class="sticky" style="top:40vh">
                    <x-slot name="title">Reservation details</x-slot>
                    <x-slot name="body">

                        <div class="divider my-1    "></div>
                        <div class="res-details">
                        <p><span>From:</span> <b>Poreč</b></p>
                        <p>To: <b>Rijeka airport</b></p>
                        <p>Departure: <b>Nov 06, 2022</b></p>
                        <p>Passengers: <b>3</b></p>
                        <p>Ticket type: <b>One way</b></p>
                        </div>
                        <div class="divider my-1    "></div>

                        <div class="alert alert-info alert-sm ">
                            <div class="text-right ml-auto text-white gap-2 pr-2">
                                Total price:  <b> 1,233 EUR</b>
                            </div>
                        </div>
                    </x-slot>
                </x-ez-card>

        </div>

    </div>


</div>
