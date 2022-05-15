<div>
    <div class="flex gap-2 pt-4 ">
        <x-ez-card class=" w-1/2 shadow-none">
            <x-slot name="title">
                From: {{$reservation->pickupLocation->name}}

            </x-slot>

            <x-slot name="body">
                <div>
                    <p><strong>Pickup Address: </strong>{{$reservation->pickup_address}} </p>
                    <p><strong>Pickup Date: </strong>{{$reservation->date->format('d.m.Y')}} </p>
                    <p><strong>Pickup Time:</strong> {{$reservation->time->format('H:i')}} </p>
                </div>
            </x-slot>
        </x-ez-card>
        @if($reservation->returnReservation)
            <i class="fas fa-exchange-alt self-center text-xl"></i>
        @else
            <i class="fas fa-long-arrow-alt-right self-center text-xl"></i>
        @endif

        <x-ez-card class="w-1/2 shadow-none">

            <x-slot name="title">
                From: {{$reservation->dropoffLocation->name}}
            </x-slot>
            <x-slot name="body">
                <div>
                    <p><strong>Dropoff Address: </strong>{{$reservation->dropoff_address}} </p>
                    @if($reservation->returnReservation)
                        <p><strong>Round Trip Pickup
                                Date: </strong>{{$reservation->returnReservation->date->format('d.m.Y')}} </p>
                        <p><strong>Round Trip Pickup
                                Time:</strong> {{$reservation->returnReservation->time->format('H:i')}} </p>

                    @endif
                </div>
            </x-slot>

        </x-ez-card>
    </div>
    <div class="divider "></div>


    <!-- TAB RESERVATION -->
    <div class="grid md:grid-cols-3 grid-cols-1 gap-2 mt-2">

        <div class="">
            <x-ez-card class="mb-4 shadow-none">

                <x-slot name="title" class="mb-0 flex justify-between">
                    <div>
                        <span class="text-md">Vehicle details</span>
                    </div>
                </x-slot>

                <x-slot name="body">
                    <div class="divider mt-0 mb-0"></div>
                    <table class="table table-compact w-full">

                        <tbody>

                        <tr>
                            <td class="font-bold">Vehicle:</td>
                            <td>{{$this->reservation->transfer->vehicle->name}}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Vehicle type:</td>
                            <td>{{$this->reservation->transfer->vehicle->type}}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Max occupancy:</td>
                            <td>{{$this->reservation->transfer->vehicle->max_occ}}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Max luggage:</td>
                            <td>{{$this->reservation->transfer->vehicle->max_luggage}}</td>
                        </tr>

                        </tbody>
                    </table>

                </x-slot>
            </x-ez-card>

            <x-ez-card class="mb-4  shadow-none">

                <x-slot name="title" class="mb-0 flex justify-between">
                    <div>
                        <span class="text-md">Partner details</span>

                    </div>
                </x-slot>

                <x-slot name="body">
                    <div class="divider mt-0 mb-0"></div>

                    <table class="table table-compact w-full">

                        <tbody>
                        <tr>
                            <td class="font-bold">Name:</td>
                            <td>{{$this->reservation->partner->name}}</td>

                        </tr>
                        <tr>
                            <td class="font-bold">Phone:</td>
                            <td>{{$this->reservation->partner->phone}}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Email:</td>
                            <td>{{$this->reservation->partner->email}}</td>
                        </tr>


                        </tbody>
                    </table>
                </x-slot>
            </x-ez-card>
        </div>

        <div class="md:col-span-1">
            <x-ez-card class="mb-4 shadow-none">

                <x-slot name="title" class="mb-0 flex justify-between">
                    <div>
                        <span class="text-md">Transfer information</span>

                    </div>
                </x-slot>

                <x-slot name="body">
                    <div class="divider mt-0 mb-0"></div>

                    <table class="table table-compact w-full">

                        <tbody>

                        <tr>
                            <td class="font-bold">Transfer:</td>
                            <td>{{$this->reservation->transfer->name}}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Adults:</td>
                            <td>{{$this->reservation->adults}}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Children:</td>
                            <td>{{$this->reservation->children}}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Infants:</td>
                            <td>{{$this->reservation->infants}}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Luggage:</td>
                            <td>{{$this->reservation->luggage}}</td>
                        </tr>


                        </tbody>
                    </table>




                    @if($this->otherTravellers->isNotEmpty())
                        <div class="divider mt-0 mb-0"></div>

                        <p class="text-xl font-extrabold">Other Travellers</p>

                        <table class="table table-compact w-full">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Title:</th>
                                <th>First Name:</th>
                                <th>Last Name:</th>
                                <th>Comment</th>
                                <th>Edit</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($this->otherTravellers as $otherTraveller)
                                <tr>

                                    <td class="text-info">{{$loop->iteration}}:</td>
                                    <td>{{$otherTraveller->title}}</td>
                                    <td> {{$otherTraveller->first_name}}</td>
                                    <td> {{$otherTraveller->last_name}}</td>
                                    <td> {{$otherTraveller->reservations->first()->pivot->comment}}</td>
                                    <td>
                                        <button wire:click="openOtherTravellerModal({{$otherTraveller->id}})"
                                                class="btn md:btn-circle btn-sm btn-success">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>



                    @endif


                </x-slot>
            </x-ez-card>
        </div>


    </div>

    <x-ez-card class="mb-4 shadow-none">

        <x-slot name="title" class="mb-0 flex justify-between">
            <div>
                <span class="text-md">Main traveller</span>

            </div>
        </x-slot>

        <x-slot name="body">
            <div class="divider mt-0 mb-0"></div>


            <table class="table table-compact w-full">

                <tbody>

                <tr>
                    <td class="font-bold">Transfer:</td>
                    <td>{{$this->reservation->transfer->name}}</td>
                </tr>
                <tr>
                    <td class="font-bold">Adults:</td>
                    <td>{{$this->reservation->adults}}</td>
                </tr>
                <tr>
                    <td class="font-bold">Children:</td>
                    <td>{{$this->reservation->children}}</td>
                </tr>
                <tr>
                    <td class="font-bold">Infants:</td>
                    <td>{{$this->reservation->infants}}</td>
                </tr>
                <tr>
                    <td class="font-bold">Luggage:</td>
                    <td>{{$this->reservation->luggage}}</td>
                </tr>


                </tbody>
            </table>






        </x-slot>
    </x-ez-card>
    @if($this->otherTravellers->isNotEmpty())

    <x-ez-card class="mb-4 shadow-none">

        <x-slot name="title" class="mb-0 flex justify-between">
            <div>
                <span class="text-md">Other travellers</span>

            </div>
        </x-slot>

        <x-slot name="body">
            <div class="divider mt-0 mb-0"></div>


                <div class="divider mt-0 mb-0"></div>


                <table class="table table-compact w-full">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title:</th>
                        <th>First Name:</th>
                        <th>Last Name:</th>
                        <th>Comment</th>
                        <th>Edit</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->otherTravellers as $otherTraveller)
                        <tr>

                            <td class="text-info">{{$loop->iteration}}:</td>
                            <td>{{$otherTraveller->title}}</td>
                            <td> {{$otherTraveller->first_name}}</td>
                            <td> {{$otherTraveller->last_name}}</td>
                            <td> {{$otherTraveller->reservations->first()->pivot->comment}}</td>
                            <td>
                                <button wire:click="openOtherTravellerModal({{$otherTraveller->id}})"
                                        class="btn md:btn-circle btn-sm btn-success">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
        </x-slot>
    </x-ez-card>
    @endif

    <div class="modal {{ $otherTravellerModal ? 'modal-open fadeIn' : '' }}">
        <div class="modal-box max-h-screen overflow-y-auto">
            Update other traveller data
            <hr class="my-4">

            <div class="form-control">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Title :</span>
                    </label>
                    <input wire:model="otherTraveller.title" class="input input-bordered"
                           placeholder="Title">
                    @error('otherTraveller.title')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>
            </div>

            <div class="form-control">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">First Name :</span>
                    </label>
                    <input wire:model="otherTraveller.first_name" class="input input-bordered"
                           placeholder="First Name">
                    @error('otherTraveller.first_name')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>
            </div>

            <div class="form-control">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Last Name :</span>
                    </label>
                    <input wire:model="otherTraveller.last_name" class="input input-bordered"
                           placeholder="Last Name">
                    @error('otherTraveller.last_name')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>
            </div>

            <div class="form-control">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Comment :</span>
                    </label>
                    <input wire:model="otherTravellerComment" class="input input-bordered"
                           placeholder="Comment">
                    @error('otherTravellerComment')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>
            </div>


            <div class="mt-4 flex justify-between">
                <button wire:click="closeOtherTravellerModal()" class="btn btn-sm ">Close</button>
                <button wire:click="saveOtherTravellerData()"
                        class="btn btn-sm ">Update
                </button>
            </div>
        </div>
    </div>


</div>
