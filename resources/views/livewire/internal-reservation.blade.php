<div class="internal-reservation container ">
    <div class="grid grid-cols-3 gap-4">
        <div class="col-span-2 ">
            @if($step === 1)
                <x-ez-card class="mb-4 ">

                    <x-slot name="title">
                        <i class="fas fa-search"></i> Search
                    </x-slot>

                    <x-slot name="body" class="">
                        <div class="grid grid-cols-2 gap-4">
                            {{--                            <div class="form-control ">--}}
                            {{--                                <select class="my-select select-sm" wire:model="stepOneFields.destinationId">--}}
                            {{--                                    <option value="">Pick a destination</option>--}}
                            {{--                                    @foreach($this->destinationsWithRoutes as $destination)--}}
                            {{--                                        <option value="{{$destination->id}}">{{$destination->name}}</option>--}}

                            {{--                                    @endforeach--}}


                            {{--                                </select>--}}

                            {{--                            </div>--}}

                            <div class="form-control ">
                                @if(!empty($this->stepOneFields['destinationId']))
                                    @if($this->startingPoints->isNotEmpty())
                                        <label class="label-text ">Pickup location</label>
                                        <select class="my-select select-sm" wire:model="stepOneFields.startingPointId">
                                            <option value="">Pickup location</option>
                                            @foreach($this->startingPoints as $point)
                                                <option value="{{$point->id}}">{{$point->name}}</option>
                                            @endforeach

                                        </select>
                                    @else
                                        <div class="alert alert-warning">No Pickup points for that destination</div>
                                    @endif
                                @endif
                                @if($this->stepOneFields['startingPointId'] && $this->stepOneFields['endingPointId'] )

                                    <x-form.ez-text-input sm label="Pickup address"
                                                          wire:model="stepOneFields.pickupAddress"></x-form.ez-text-input>
                                @endif
                            </div>
                            @if(!empty($this->stepOneFields['startingPointId']))
                                <div class="form-control ">
                                    <label class="label-text ">Dropoff location</label>

                                    @if($this->endingPoints->isNotEmpty())
                                        <select class="my-select select-sm" wire:model="stepOneFields.endingPointId">
                                            <option value="">Drop off location</option>
                                            @foreach($this->endingPoints as $point)
                                                <option value="{{$point->id}}">{{$point->name}}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <div class="alert alert-warning">No Dropoff points for that pickup point!</div>

                                    @endif
                                    @if($this->stepOneFields['startingPointId'] && $this->stepOneFields['endingPointId'] )

                                        <x-form.ez-text-input sm label="Dropoff address"
                                                              wire:model="stepOneFields.dropoffAddress"></x-form.ez-text-input>
                                    @endif
                                </div>
                            @endif

                        </div>
                        @if(!empty($stepOneFields['destinationId']) && !empty($stepOneFields['startingPointId']) && !empty($stepOneFields['endingPointId']))
                            <div x-data="{open: false}" x-show="open" x-transition
                                 x-init="setTimeout(() => { open = true })">


                                <div class="divider my-1    "></div>
                                <div class="gap-2">
                                    <div class="grid grid-cols-4  gap-2">
                                        <div class="form-control  ">
                                            <label class="label">
                                                <span class="label-text">Date:</span>
                                            </label>
                                            <input x-init="
                                        flatpickr($el, {
                                        disableMobile: 'true',
                                        minDate:'today',
                                        dateFormat:'d.m.Y',
                                        defaultDate:'{{$stepOneFields['dateTo']}}'});
                                        " readonly
                                                   wire:model="stepOneFields.dateTo"
                                                   class=" input input-bordered input-sm mt-2"
                                                   placeholder="Date to:">

                                        </div>
                                        <div class="form-control ">
                                            <label class="label">
                                                <span class="label-text">Time:</span>
                                            </label>
                                            <input x-init='
                                        flatpickr($el, {
                                        disableMobile: "true",
                                        enableTime: true,
                                        noCalendar: true,
                                        dateFormat: "H:i",
                                        time_24hr: true,
                                        defaultDate:"{{$stepOneFields['timeTo']}}"});
                                        ' readonly
                                                   wire:model="stepOneFields.timeTo"
                                                   class="ml-2 input input-bordered input-sm mt-2"
                                                   placeholder="Time to:">

                                        </div>

                                        @if($twoWay)


                                            <div class="form-control  ">
                                                <label class="label">
                                                    <span class="label-text">Date:</span>
                                                </label>
                                                <input x-init="
                                        flatpickr($el, {
                                        disableMobile: 'true',
                                        minDate:'today',
                                        dateFormat:'d.m.Y',
                                        defaultDate:'{{$stepOneFields['dateFrom']}}'});
                                        " readonly
                                                       wire:model="stepOneFields.dateFrom"
                                                       class="ml-2 input input-bordered input-sm mt-2"
                                                       placeholder="Date from:">

                                            </div>
                                            <div class="form-control ">
                                                <label class="label">
                                                    <span class="label-text">Time:</span>
                                                </label>
                                                <input x-init='
                                        flatpickr($el, {
                                        disableMobile: "true",
                                        enableTime: true,
                                        noCalendar: true,
                                        dateFormat: "H:i",
                                        time_24hr: true,

                                        defaultDate:"{{$stepOneFields['timeFrom']}}"});
                                        ' readonly
                                                       wire:model="stepOneFields.timeFrom"
                                                       class="ml-2 input input-bordered input-sm mt-2"
                                                       placeholder="Time from:">

                                            </div>
                                        @endif
                                        <div @class([   'form-control',
                                                        'col-span-2'=>!$this->twoWay,
                                                        'col-span-4'=>$this->twoWay])>

                                            <label
                                                class="label cursor-pointer ml-auto mr-2 {{!$twoWay? 'mt-10':'mt-2'}}  mb-1  ">
                                            <span class="label-text mr-2">
                                              <i class="fas fa-exchange-alt mx-4"></i>
                                                 Two way</span>
                                                <input type="checkbox" wire:model="twoWay" class="checkbox">
                                            </label>

                                        </div>
                                    </div>


                                </div>


                                <div class="divider my-1    "></div>

                                <div class="">
                                    <div class="flex flex-wrap gap-2">


                                        <div class="form-control  ">
                                            <label class="label">
                                                <span class="label-text">Adult:</span>
                                            </label>
                                            <input class="my-input input-sm w-full" placeholder=""
                                                   wire:model="stepOneFields.adults">

                                        </div>


                                        <div class="form-control  ">
                                            <label class="label">
                                                <span class="label-text">Child(3-17):</span>
                                            </label>
                                            <input class="my-input input-sm w-full" placeholder=""
                                                   wire:model="stepOneFields.children">

                                        </div>
                                        <div class="form-control  ">
                                            <label class="label">
                                                <span class="label-text">Infant(0-2):</span>
                                            </label>
                                            <input class="my-input input-sm w-full" placeholder=""
                                                   wire:model="stepOneFields.infants">

                                        </div>
                                        <div class="form-control  ">
                                            <label class="label">
                                                <span class="label-text">Luggage</span>
                                            </label>
                                            <input class="my-input input-sm " placeholder=""
                                                   wire:model="stepOneFields.luggage">

                                        </div>
                                    </div>


                                </div>
                            </div>
                        @endif
                    </x-slot>
                </x-ez-card>

            @endif



            @if($step === 1)

                @if($this->availableTransfers->isNotEmpty() && !empty($stepOneFields['destinationId']) && !empty($stepOneFields['startingPointId']) && !empty($stepOneFields['endingPointId']))
                    <div x-data="{open: false}" x-show="open" x-transition
                         x-init="setTimeout(() => { open = true })">

                        <x-ez-card class="">
                            <x-slot name="title">Transfers</x-slot>
                            <x-slot name="body">
                                @foreach($this->availableTransfers as $transfer)
                                    <div class="card  bg-gray-200 ">
                                        <div class="card-body p-2">

                                            <div class="flex gap-4">
                                                <div class="basis-1/5">
                                                    <img class="h-24 w-full object-cover rounded-xl"
                                                         src="{{$transfer->primaryImageUrl}}"/>
                                                </div>
                                                <div class="basis-4/5">
                                                    <h2 class="card-title mb-2">{{$transfer->name}}</h2>
                                                    <div class="flex gap-4 mb-2">
                                                        <span class=" ">Type: Van</span>
                                                        <span class=" ">Max. Occ: {{$transfer->vehicle->max_occ}}</span>
                                                        <span
                                                            class=" ">Max. Luggage:{{$transfer->vehicle->max_luggage}}</span>

                                                    </div>
                                                    <span class="  ">Price: <b> {{Cknow\Money\Money::EUR($transfer->pivot->price)}} EUR</b></span>
                                                    <div class="badge badge-info top-2 right-2 absolute">
                                                        {{$transfer->partner->name}}
                                                    </div>
                                                    <button
                                                        class="btn btn-sm btn-primary absolute bottom-2 rounded-xl right-2"
                                                        wire:click="selectTransfer({{$transfer->id}},{{$transfer->partner->id}})">
                                                        Select
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach


                            </x-slot>
                        </x-ez-card>
                    </div>
                @endif
            @endif


            @if($step === 2)
                <div>
                    <x-ez-card class="mb-4">
                        <x-slot name="title">Transfer details</x-slot>
                        <x-slot name="body">
                            <div class="grid grid-cols-3 gap-4">

                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Flight number {{$twoWay?'#1':''}}"
                                                          value="31782563"></x-form.ez-text-input>
                                </div>

                                @if($twoWay)
                                    <div class="col-span-1">
                                        <x-form.ez-text-input sm label="Flight number #2"
                                                              value="1872351"></x-form.ez-text-input>
                                    </div>
                                @endif


                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Remark"></x-form.ez-text-input>
                                </div>
                            </div>

                        </x-slot>
                    </x-ez-card>
                    @if($stepOneFields['children']>1 || $stepOneFields['infants']>1)
                        <x-ez-card class="mb-4">
                            <x-slot name="title">Child seats</x-slot>
                            <x-slot name="body">
                                <div class="grid grid-cols-4 gap-4">
                                    @foreach($seats as $seat)
                                        <div class="col-span-2">
                                            <x-form.ez-select model="" :label="'Seat #'.$loop->index +1 .':'"
                                                              :items="['Booster (10-15 kg)','Egg (0-5 kg)','Classic(5-10 kg)']"
                                                              sm="true"></x-form.ez-select>
                                        </div>


                                    @endforeach

                                </div>
                                <div class="flex justify-end gap-4">

                                    <button class="btn btn-outline  btn-sm btn-circle" wire:click="addSeat"><i
                                            class="fas fa-plus"></i></button>
                                    <button class="btn btn-outline  btn-sm btn-circle" wire:click="removeSeat"><i
                                            class="fas fa-minus"></i></button>
                                </div>

                            </x-slot>
                        </x-ez-card>

                    @endif
                    <x-ez-card class="mb-4">
                        <x-slot name="title">
                            <div class="flex justify-between w-full">
                                <span>                              Lead traveller details

                                </span>
                                <div>
                                    <x-ez-modal>
                                        <x-slot name="button" class="btn-sm">Pull traveller</x-slot>

                                        <x-form.ez-text-input sm label="Reservation id"
                                                              value="3127863"></x-form.ez-text-input>
                                        <x-slot name="footer">
                                            <label for="ez-modal" wire:click="pullTraveller"
                                                   class="btn btn-sm btn-success">
                                                Pull data
                                            </label>
                                        </x-slot>

                                    </x-ez-modal>
                                </div>
                            </div>

                        </x-slot>
                        <x-slot name="body">
                            <div class="grid grid-cols-3 gap-4">

                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Title"
                                                          wire:model="stepTwoFields.leadTraveller.title"
                                    ></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="First name"
                                                          wire:model="stepTwoFields.leadTraveller.firstName"
                                    ></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Last name"
                                                          wire:model="stepTwoFields.leadTraveller.lastName"
                                    ></x-form.ez-text-input>

                                </div>

                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Reservation number"
                                                          wire:model="stepTwoFields.leadTraveller.reservationNumber"
                                    ></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Email"
                                                          wire:model="stepTwoFields.leadTraveller.email"
                                    ></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Phone"
                                                          wire:model="stepTwoFields.leadTraveller.phone"
                                    ></x-form.ez-text-input>

                                </div>


                            </div>

                        </x-slot>
                    </x-ez-card>
                    @if($this->totalPassengers >1)
                        <x-ez-card>
                            <x-slot name="title">Other traveller details</x-slot>
                            <x-slot name="body">
                                <div class="grid grid-cols-4 gap-4">
                                    @foreach($this->stepTwoFields['otherTravellers'] as $i => $traveler)
                                        <div class="col-span-1">
                                            <x-form.ez-text-input sm label="Title"
                                                                  wire:model="stepTwoFields.otherTravellers.{{$i}}.title"></x-form.ez-text-input>
                                        </div>
                                        <div class="col-span-1">
                                            <x-form.ez-text-input sm label="First name"
                                                                  wire:model="stepTwoFields.otherTravellers.{{$i}}.firstName"
                                            ></x-form.ez-text-input>
                                        </div>
                                        <div class="col-span-1">
                                            <x-form.ez-text-input sm label="Last name"
                                                                  wire:model="stepTwoFields.otherTravellers.{{$i}}.lastName"
                                            ></x-form.ez-text-input>
                                        </div>

                                        <div class="col-span-1">
                                            <x-form.ez-text-input sm label="Comment"
                                                                  wire:model="stepTwoFields.otherTravellers.{{$i}}.comment"
                                            ></x-form.ez-text-input>
                                        </div>

                                    @endforeach

                                </div>


                            </x-slot>
                        </x-ez-card>

                    @endif
                </div>

            @endif
        </div>
        @if($this->availableTransfers->isNotEmpty() && !empty($stepOneFields['destinationId']) && !empty($stepOneFields['startingPointId']) && !empty($stepOneFields['endingPointId']))
            <div x-data="{open: false}" x-show="open" x-transition
                 x-init="setTimeout(() => { open = true })">
                <div class="col-span-1 ">
                    <x-ez-card class="sticky" style="top:5vh">
                        <x-slot name="title">Reservation details</x-slot>
                        <x-slot name="body">

                            <div class="divider my-1    "></div>
                            <div class="res-details">
                                @if($this->selectedStartingPoint)
                                    <p><span>From:</span> <b>{{$this->selectedStartingPoint->name}}</b></p>

                                    @if($this->stepOneFields['pickupAddress'])
                                        <p><span>Address:</span> <b>{{$this->stepOneFields['pickupAddress']}}</b></p>
                                    @endif
                                    <div class="divider my-1    "></div>

                                @endif
                                @if($this->selectedEndingPoint)

                                    <p>To: <b>{{$this->selectedEndingPoint->name}}</b></p>

                                    @if($this->stepOneFields['dropoffAddress'])
                                        <p><span>Address:</span> <b>{{$this->stepOneFields['dropoffAddress']}}</b></p>
                                    @endif
                                        <div class="divider my-1    "></div>

                                    @endif
                                @if(!empty($this->stepOneFields['dateTo']))
                                    <p>Date to:
                                        <b>{{\Carbon\Carbon::make($this->stepOneFields['dateTo'])->format('d.m.Y')}}</b>
                                    </p>

                                @endif
                                @if(!empty($this->stepOneFields['timeTo']))
                                    <p>Time to:
                                        <b>{{\Carbon\Carbon::make($this->stepOneFields['timeTo'])->format('H:i')}}</b>
                                    </p>

                                @endif
                                @if(!empty($this->stepOneFields['dateFrom']))
                                    <p>Time from:
                                        <b>{{\Carbon\Carbon::make($this->stepOneFields['dateFrom'])->format('d.m.Y')}}</b>
                                    </p>

                                @endif
                                @if(!empty($this->stepOneFields['timeFrom']))
                                    <p>Time from:
                                        <b>{{\Carbon\Carbon::make($this->stepOneFields['timeFrom'])->format('H:i')}}</b>
                                    </p>

                                @endif
                                <p>Passengers: <b>{{$this->totalPassengers}}</b></p>
                                <p>Ticket type: <b>{{$this->twoWay ? 'Two way' : 'One way'}}</b></p>
                            </div>
                            <div class="divider my-1    "></div>

                            @if($this->totalPrice)
                                <div class="alert alert-info alert-sm ">
                                    <div class="text-right ml-auto text-white gap-2 pr-2">
                                        Total price: <b> {{$this->totalPrice}} EUR</b>
                                    </div>
                                </div>
                            @endif

                        </x-slot>
                    </x-ez-card>

                    @if($step === 2)
                        <x-ez-card class="mt-4">
                            <x-slot name="body">
                                <button class="btn btn-large btn-accent rounded-box"><span class="mr-4">Complete reservation</span>
                                    <i class="fas fa-arrow-right float-right"></i></button>
                            </x-slot>
                        </x-ez-card>
                    @endif

                </div>
            </div>
        @endif

    </div>


</div>
