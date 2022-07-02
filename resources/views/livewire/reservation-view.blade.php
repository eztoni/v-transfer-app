<div>


    <div class="alert

    @switch($reservation->status)
    @case(\App\Models\Reservation::STATUS_CONFIRMED)
        alert-success
        @break
    @case(\App\Models\Reservation::STATUS_CANCELLED)
        alert-error
        @break
    @case(\App\Models\Reservation::STATUS_PENDING)
        alert-warning

    @break
    @endswitch

        ">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>Reservation status: {{$reservation->status}}</span>
        </div>
    </div>

    <div class="flex gap-2 pt-4 ">
        <x-ez-card class=" w-1/2 shadow-none border-secondary relative">
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
            <i class="fas fa-exchange-alt self-center text-3xl text-secondary"></i>
        @else
            <i class="fas fa-long-arrow-alt-right self-center text-3xl text-secondary"></i>
        @endif

        <x-ez-card class="w-1/2 shadow-none border-secondary">

            <x-slot name="title">
                To: {{$reservation->dropoffLocation->name}}
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
    <div class="grid md:grid-cols-2  gap-2 mt-2">

        <div class="col-span-1">
            <x-ez-card class="mb-4 shadow-none border-none">

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


        </div>
        <div class="col-span-1">
            <x-ez-card class="mb-4  shadow-none border-none">

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

        <div class="col-span-1">
            <x-ez-card class="mb-4 shadow-none border-none">

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
                        <tr>
                            <td class="font-bold">Flight number:</td>
                            <td>{{$this->reservation->flight_number}}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Remark:</td>
                            <td>{{$this->reservation->remark}}</td>
                        </tr>
                        </tbody>
                    </table>


                </x-slot>
            </x-ez-card>
        </div>

        <div class="col-span-1">
            <x-ez-card class="mb-4 shadow-none border-none">

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
                            <td class="font-bold">Title:</td>
                            <td>{{$this->reservation->leadTraveller->title}}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">First name:</td>
                            <td>{{$this->reservation->leadTraveller->first_name}}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Last name:</td>
                            <td>{{$this->reservation->leadTraveller->last_name}}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Reservation number:</td>
                            <td>{{$this->reservation->leadTraveller->reservation_number}}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Phone:</td>
                            <td>{{$this->reservation->leadTraveller->phone}}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Email:</td>
                            <td>{{$this->reservation->leadTraveller->email}}</td>
                        </tr>


                        </tbody>
                    </table>


                </x-slot>
            </x-ez-card>

        </div>
        @if(!empty($reservation->child_seats))
            <div class="col-span-1">
                <x-ez-card class="mb-4 shadow-none border-none">

                    <x-slot name="title" class="mb-0 flex justify-between">
                        <div>
                            <span class="text-md">Child seats</span>

                        </div>
                    </x-slot>

                    <x-slot name="body">
                        <div class="divider mt-0 mb-0"></div>


                        <table class="table table-compact w-full">
                            <tbody>
                            @foreach($reservation->child_seats as $seat)
                                <tr>
                                    <td class="font-bold">Seat #{{$loop->index+1}}:</td>
                                    <td>{{\App\Models\Transfer::CHILD_SEATS[$seat]}}</td>
                                </tr>
                            @endforeach




                            </tbody>
                        </table>


                    </x-slot>
                </x-ez-card>

            </div>

        @endif
        @if($this->otherTravellers->isNotEmpty())

            <div class="col-span-1">

                <x-ez-card class="mb-4 shadow-none border-none">

                    <x-slot name="title" class="mb-0 flex justify-between">
                        <span class="text-md">Other travellers</span>

                    </x-slot>

                    <x-slot name="body">


                        <div class="divider mt-0 mb-0"></div>


                        <table class="table table-compact w-full">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Title:</th>
                                <th>First Name:</th>
                                <th>Last Name:</th>
                                <th>Comment</th>
                                <th class="text-center">Edit</th>
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
                                    <td class="text-center">
                                        <button wire:click="openOtherTravellerModal({{$otherTraveller->id}})"
                                                class="btn  btn-sm btn-outline">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </x-slot>
                </x-ez-card>

            </div>
        @endif

        @if($this->otherTravellers->isNotEmpty())

            <div class="col-span-1">

                <x-ez-card class="mb-4 shadow-none border-none">

                    <x-slot name="title" class="mb-0 flex justify-between">
                        <span class="text-md">Extras</span>

                    </x-slot>

                    <x-slot name="body">


                        <div class="divider mt-0 mb-0"></div>


                        <table class="table table-compact w-full">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name:</th>
                                <th>Description:</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($this->reservation->extras as $extra)
                                <tr>
                                    <td class="text-info">{{$extra->id}}</td>
                                    <td>{{$extra->name}}</td>
                                    <td> {{Str::limit($extra->description,60)}}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </x-slot>
                </x-ez-card>

            </div>
        @endif
        <div class="col-span-1">

            <x-ez-card class="mb-4 shadow-none border-none">

                <x-slot name="title" class="mb-0 flex justify-between">
                    <span class="text-md">Price breakdown</span>

                </x-slot>

                <x-slot name="body">


                    <div class="divider mt-0 mb-0"></div>


                    <table class="table table-compact w-full">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name:</th>
                            <th class="text-right">Amount:</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($this->reservation->price_breakdown as $pbItem)
                            <tr>
                                <td class="text-info">{{$loop->index+1}}</td>
                                <td>{{Arr::get($pbItem,'name')}}</td>
                                <td class="text-right"> {{Arr::get($pbItem,'amount.formatted')}}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td class="text-info"></td>
                            <td class="font-bold text-lg">TOTAL:</td>
                            <td  class="font-bold text-lg text-right"> {{\Cknow\Money\Money::EUR($reservation->price)}}</td>
                        </tr>
                        </tbody>
                    </table>
                </x-slot>
            </x-ez-card>

        </div>
    </div>






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
