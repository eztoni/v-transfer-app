<div class="internal-reservation container " x-data="app()">
    <div class="grid grid-cols-3 gap-4">
        <div class="col-span-2 ">
            @if($step === 1)
                <x-ez-card class="mb-4 ">

                    <x-slot name="title">
                        <i class="fas fa-search"></i> Search

                        <span class="ml-auto">
                            <label for="internal-modal" class="btn btn-sm btn-outline">Pull data</label>
                        </span>

                        <x-ez-modal id="internal-modal" lg>

                            <div class="flex gap-4 justify-between flex-wrap">
                                <x-form.ez-text-input sm label="Reservation ID"></x-form.ez-text-input>
                                <x-form.ez-text-input sm label="Room number"></x-form.ez-text-input>
                                <x-form.ez-text-input sm label="Guest name"></x-form.ez-text-input>
                                <x-form.ez-text-input sm label="Guest last name"></x-form.ez-text-input>

                            </div>
                            <hr class="my-4">
                            <table class="table table-compact w-full">
                                <thead>
                                <tr>
                                    <th>#ResId</th>
                                    <th>Name</th>
                                    <th>Lastname</th>
                                    <th>Room no</th>
                                    <th>Object</th>
                                    <th>From</th>
                                    <th>to</th>
                                    <th>Pull</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th>1</th>
                                    <td>Joanna</td>
                                    <td>Ganderton</td>
                                    <td>311</td>
                                    <td>Lacroma</td>
                                    <td>12.07.2022</td>
                                    <td>18.07.2022</td>
                                    <td>
                                        <button class="btn btn-xs"><i class="fas fa-download"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <th>1</th>
                                    <td>Joanna</td>
                                    <td>Ganderton</td>
                                    <td>311</td>
                                    <td>Lacroma</td>
                                    <td>12.07.2022</td>
                                    <td>18.07.2022</td>
                                    <td>
                                        <button class="btn btn-xs"><i class="fas fa-download"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <th>1</th>
                                    <td>Joanna</td>
                                    <td>Ganderton</td>
                                    <td>311</td>
                                    <td>Lacroma</td>
                                    <td>12.07.2022</td>
                                    <td>18.07.2022</td>
                                    <td>
                                        <button class="btn btn-xs"><i class="fas fa-download"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <th>1</th>
                                    <td>Joanna</td>
                                    <td>Ganderton</td>
                                    <td>311</td>
                                    <td>Lacroma</td>
                                    <td>12.07.2022</td>
                                    <td>18.07.2022</td>
                                    <td>
                                        <button class="btn btn-xs"><i class="fas fa-download"></i></button>

                                    </td>
                                </tr>


                                </tbody>
                            </table>
                        </x-ez-modal>

                    </x-slot>

                    <x-slot name="body" class="">
                        <div class="grid grid-cols-2 gap-4">
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
                                        @error('stepOneFields.startingPointId')
                                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                        @enderror
                                    @else
                                        <div class="alert alert-warning">No Pickup points for that destination</div>
                                    @endif
                                @endif
                                @if($this->stepOneFields['startingPointId'] && $this->stepOneFields['endingPointId'] )

                                    <div class="form-control pt-2" wire:ignore>
                                        <label class="label-text ">Pickup address</label>
                                        <select id="pickupSelect" x-init=" $(' #pickupSelect').select2(
                                        {
                                        closeOnSelect: true,
                                        tags: true,
                                        placeholder: 'Select or type pickup address',


                                        }
                                        ).on('change', function (e) {
                                        @this.
                                        set('stepOneFields.pickupAddress', $('#pickupSelect').select2('val'))
                                        })

                                        ">
                                            <option></option>

                                            @php
                                                $itemSelected = false;
                                            @endphp

                                            @foreach($this->pickupAddressPoints as $pickupAddressPoint)



                                                <option
                                                    @if($this->stepOneFields['pickupAddress'] === $pickupAddressPoint->name. ' ' . $pickupAddressPoint->address)
                                                    selected

                                                    @php
                                                        $itemSelected = true;
                                                    @endphp

                                                    @endif
                                                    value="{{$pickupAddressPoint->name. ' ' . $pickupAddressPoint->address}}">{{$pickupAddressPoint->name. ' ' . $pickupAddressPoint->address}}</option>

                                            @endforeach

                                            @if($itemSelected === false)
                                                <option
                                                    value="{{$this->stepOneFields['pickupAddress']}}" selected>
                                                    {{$this->stepOneFields['pickupAddress']}}</option>
                                            @endif
                                        </select>
                                    </div>
                                    @error('stepOneFields.pickupAddress')
                                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                    @enderror
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
                                        @error('stepOneFields.endingPointId')
                                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                        @enderror
                                    @else
                                        <div class="alert alert-warning">No Dropoff points for that pickup point!</div>

                                    @endif
                                    @if($this->stepOneFields['startingPointId'] && $this->stepOneFields['endingPointId']  )
                                        <div class="form-control pt-2" wire:ignore>
                                            <label class="label-text ">Dropoff address</label>

                                            <select id="dropoffSelect" x-init=" $('#dropoffSelect').select2(
                                                {
                                                    closeOnSelect: true,
                                                    tags: true,
                                                      placeholder: 'Select or type dropoff address',
                                                }
                                            ).on('change', function (e) {
                                                @this.
                                                set('stepOneFields.dropoffAddress', $('#dropoffSelect').val())
                                            })
                                            ">
                                                <option></option>
                                                @php
                                                    $itemSelected = false;
                                                @endphp
                                                @foreach($this->dropoffAddressPoints as $dropoffAddressPoint)
                                                    <option
                                                        @if($this->stepOneFields['dropoffAddress'] === $dropoffAddressPoint->name. ' ' . $dropoffAddressPoint->address)
                                                        selected

                                                        @php
                                                            $itemSelected = true;
                                                        @endphp

                                                        @endif
                                                        value="{{$dropoffAddressPoint->name . ' ' . $dropoffAddressPoint->address}}">{{$dropoffAddressPoint->name. ' ' . $dropoffAddressPoint->address}}</option>
                                                @endforeach
                                                @if($itemSelected === false)
                                                    <option
                                                        value="{{$this->stepOneFields['dropoffAddress']}}" selected>
                                                        {{$this->stepOneFields['dropoffAddress']}}</option>
                                                @endif
                                            </select>

                                        </div>
                                        @error('stepOneFields.dropoffAddress')
                                        <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                        @enderror
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
                                        defaultDate:'{{$stepOneFields['date']}}'});
                                        " readonly
                                                   wire:model="stepOneFields.date"
                                                   class=" input input-bordered input-sm mt-2"
                                                   placeholder="Date to:">

                                            @error('stepOneFields.date')
                                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                            @enderror
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
                                        defaultDate:"{{$stepOneFields['time']}}"});
                                        ' readonly
                                                   wire:model="stepOneFields.time"
                                                   class="ml-2 input input-bordered input-sm mt-2"
                                                   placeholder="Time to:">
                                            @error('stepOneFields.time')
                                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                            @enderror
                                        </div>

                                        @if($roundTrip)


                                            <div class="form-control  ">
                                                <label class="label">
                                                    <span class="label-text">Date:</span>
                                                </label>
                                                <input x-init="
                                        flatpickr($el, {
                                        disableMobile: 'true',
                                        minDate:'today',
                                        dateFormat:'d.m.Y',
                                        defaultDate:'{{$stepOneFields['returnDate']}}'});
                                        " readonly
                                                       wire:model="stepOneFields.returnDate"
                                                       class="ml-2 input input-bordered input-sm mt-2"
                                                       placeholder="Date from:">
                                                @error('stepOneFields.returnDate')
                                                <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                                @enderror
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

                                        defaultDate:"{{$stepOneFields['returnTime']}}"});
                                        ' readonly
                                                       wire:model="stepOneFields.returnTime"
                                                       class="ml-2 input input-bordered input-sm mt-2"
                                                       placeholder="Time from:">
                                                @error('stepOneFields.returnTime')
                                                <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                                @enderror
                                            </div>
                                        @endif
                                        <div @class([   'form-control',
                                                        'col-span-2'=>!$this->roundTrip,
                                                        'col-span-4'=>$this->roundTrip])>
                                            <label
                                                class="label cursor-pointer ml-auto mr-2 {{!$roundTrip? 'mt-10':'mt-2'}}  mb-1  ">
                                            <span class="label-text mr-2">
                                              <i class="fas fa-exchange-alt mx-4"></i>Round trip</span>
                                                <input type="checkbox" wire:model="roundTrip" class="checkbox">
                                            </label>
                                        </div>
                                    </div>


                                </div>


                                <div class="divider my-1    "></div>

                                <div class="">
                                    <div class="flex flex-wrap justify-between gap-2">


                                        <div class="form-control  ">
                                            <label class="label">
                                                <span class="label-text">Adult:</span>
                                            </label>
                                            <input class="my-input input-sm w-full" placeholder=""
                                                   wire:model="stepOneFields.adults">
                                            @error('stepOneFields.adults')
                                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                            @enderror
                                        </div>


                                        <div class="form-control  ">
                                            <label class="label">
                                                <span class="label-text">Child(3-17):</span>
                                            </label>
                                            <input class="my-input input-sm w-full" placeholder=""
                                                   wire:model="stepOneFields.children">
                                            @error('stepOneFields.children')
                                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                            @enderror
                                        </div>
                                        <div class="form-control  ">
                                            <label class="label">
                                                <span class="label-text">Infant(0-2):</span>
                                            </label>
                                            <input class="my-input input-sm w-full" placeholder=""
                                                   wire:model="stepOneFields.infants">
                                            @error('stepOneFields.infants')
                                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                            @enderror
                                        </div>
                                        <div class="form-control  ">
                                            <label class="label">
                                                <span class="label-text">Luggage</span>
                                            </label>
                                            <input class="my-input input-sm " placeholder=""
                                                   wire:model="stepOneFields.luggage">
                                            @error('stepOneFields.luggage')
                                            <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                            @enderror
                                        </div>
                                    </div>


                                </div>
                            </div>
                        @endif
                    </x-slot>
                </x-ez-card>

            @endif



            @if($step === 1)
                @if($this->availableTransfers->isEmpty()&& !empty($stepOneFields['destinationId']) && !empty($stepOneFields['startingPointId']) && !empty($stepOneFields['endingPointId']))
                    <div x-data="{open: false}" x-show="open" x-transition
                         x-init="setTimeout(() => { open = true })">

                        <div class="alert alert-info  shadow-lg ">
                            <div>
                                <i class="fas fa-search text-lg"></i>
                                <div>
                                    <h3 class="font-bold">No transfers for selected route!</h3>
                                    <div class="text-xs">Try changing the route, or add a transfer to route.</div>
                                </div>
                            </div>
                        </div>

                    </div>
                @endif
                @if($this->availableTransfers->isNotEmpty() && !empty($stepOneFields['destinationId']) && !empty($stepOneFields['startingPointId']) && !empty($stepOneFields['endingPointId']))
                    <div x-data="{open: false}" x-show="open" x-transition
                         x-init="setTimeout(() => { open = true })">

                        <x-ez-card class="mb-2">
                            <x-slot name="body">
                                <div class="card-title "><i class="fas fa-search text-lg"></i>
                                    Search results:
                                </div>
                            </x-slot>

                        </x-ez-card>

                        @php

                            $lastTransfer = null;

                        @endphp
                        @foreach($this->availableTransfers as $transfer)



                            @if(!$lastTransfer ||($lastTransfer && $lastTransfer->partner->id !== $transfer->partner->id))

                                @if($lastTransfer)
                    </div>
                @endif


                <div
                    class="border-2  gap-2 mb-2 bg-gradient-to-b from-primary to-white pb-2 flex flex-col rounded-box relative shadow-lg">
                    <div class="w-full flex justify-between text-neutral-content px-4 pt-1 text-lg font-bold "
                    >
                        <span>  {{$transfer->partner->id}}</span>
                        <a class="font-medium link"
                           href="tel:{{$transfer->partner->phone}}">{{$transfer->partner->phone}}</a>
                    </div>

                    @endif

                    @php
                        $lastTransfer = $transfer;
                    @endphp
                    <div class="card rounded-none bg-base-100  ">
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
                                        <span
                                            class=" ">Max. Occ: {{$transfer->vehicle->max_occ}}</span>
                                        <span
                                            class=" ">Max. Luggage:{{$transfer->vehicle->max_luggage}}</span>

                                    </div>

                                    <span class="  ">Price: <b>
                                     {{\App\Facades\EzMoney::format($this->roundTrip
                                            ?$transfer->pivot->price_round_trip
                                            :$transfer->pivot->price)}}
                                            EUR</b></span>

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
                </div>


        </div>
        @endif
        @endif


        @if($step === 2)
            <div>
                <x-ez-card class="mb-4">

                    <x-slot name="body">
                        <div class="flex justify-end">
                            <button class="btn btn-outline btn-sm" wire:click="goBack"><i
                                    class="fas fa-angle-left mr-2"></i> Back
                            </button>
                        </div>
                    </x-slot>
                </x-ez-card>
                <x-ez-card class="mb-4">
                    <x-slot name="title">Transfer details</x-slot>
                    <x-slot name="body">
                        <div class="grid grid-cols-3 gap-4">


                            <div class="col-span-1">

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Remark:</span>
                                    </label>
                                    <textarea rows="1" wire:model="stepTwoFields.remark"
                                              class="textarea textarea-bordered"
                                    ></textarea>
                                    @error('stepTwoFields.remark')
                                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                                    @enderror
                                </div>

                            </div>
                            <div class="col-span-1">
                                <x-form.ez-text-input model="stepTwoFields.arrivalFlightNumber" sm
                                                      label="Flight number {{$roundTrip?'#1':''}}"
                                ></x-form.ez-text-input>
                            </div>

                            @if($roundTrip)
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Flight number #2"
                                                          model="stepTwoFields.departureFlightNumber"
                                                          value="1872351"></x-form.ez-text-input>
                                </div>
                            @endif
                        </div>

                    </x-slot>
                </x-ez-card>

                <x-ez-card class="mb-4">
                    <x-slot name="title">
                        <div class="flex justify-between w-full">
                                <span>                              Lead traveller details

                                </span>

                        </div>

                    </x-slot>
                    <x-slot name="body">
                        <div class="grid grid-cols-3 gap-4">

                            <div class="col-span-1">
                                <x-form.ez-text-input sm label="Title" model="stepTwoFields.leadTraveller.title"

                                ></x-form.ez-text-input>
                            </div>
                            <div class="col-span-1">
                                <x-form.ez-text-input sm label="First name"
                                                      model="stepTwoFields.leadTraveller.firstName"
                                ></x-form.ez-text-input>
                            </div>
                            <div class="col-span-1">
                                <x-form.ez-text-input sm label="Last name"
                                                      model="stepTwoFields.leadTraveller.lastName"
                                ></x-form.ez-text-input>

                            </div>

                            <div class="col-span-1">
                                <x-form.ez-text-input sm label="Reservation number"
                                                      model="stepTwoFields.leadTraveller.reservationNumber"
                                ></x-form.ez-text-input>
                            </div>
                            <div class="col-span-1">
                                <x-form.ez-text-input sm label="Email"
                                                      model="stepTwoFields.leadTraveller.email"
                                ></x-form.ez-text-input>
                            </div>
                            <div class="col-span-1">
                                <x-form.ez-text-input sm label="Phone"
                                                      model="stepTwoFields.leadTraveller.phone"
                                ></x-form.ez-text-input>

                            </div>


                        </div>

                    </x-slot>
                </x-ez-card>


                <div>
                    <label
                        class="label cursor-pointer ml-auto justify-end mr-2   mb-1  ">
                                            <span class="label-text mr-2">
                                              <i class="fas fa-plus-circle"></i>Activate extras</span>
                        <input type="checkbox" wire:model="activateExtras" class="checkbox">
                    </label>
                </div>
                @if($this->activateExtras)
                    <x-ez-card class="mb-4">
                        <x-slot name="title">Extras</x-slot>
                        <x-slot name="body">
                            @if($this->extras->isNotEmpty())

                                <table class="table w-full">
                                    <!-- head -->
                                    <thead>
                                    <tr>
                                        <th>
                                            Select
                                        </th>
                                        <th>Extra</th>
                                        <th>Price</th>
                                    </tr>
                                    </thead>
                                    <tbody>


                                    @foreach($this->extras as $extra)
                                        <tr>
                                            <th>
                                                <label>
                                                    <input type="checkbox"
                                                           wire:model="stepTwoFields.extras.{{$extra->id}}"
                                                           class="checkbox">
                                                </label>
                                            </th>
                                            <td>
                                                <div class="flex items-center space-x-3">
                                                    <div class="avatar">
                                                        <div class="mask mask-squircle w-12 h-12">
                                                            <img src="{{$extra->primaryImageUrl}}"/>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="font-bold"> {{$extra->name}}</div>
                                                        <div
                                                            class="text-sm opacity-50">{{Str::limit($extra->description,70)}}</div>
                                                    </div>
                                                </div>


                                            </td>

                                            <td>{{Cknow\Money\Money::EUR($extra->partner->first()->pivot->price)}}</td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <x-input-alert type="warning">No extras for selected partner</x-input-alert>

                            @endif

                        </x-slot>
                    </x-ez-card>

                @endif

                @if(($stepOneFields['children']>1 || $stepOneFields['infants']>1))

                    <div>
                        <label
                            class="label cursor-pointer ml-auto justify-end mr-2   mb-1  ">
                                            <span class="label-text mr-2">
                                              <i class="fas fa-child"></i>Activate child seats</span>
                            <input type="checkbox" wire:model="activateChildSeats" class="checkbox">
                        </label>
                    </div>

                    @if($this->activateChildSeats )
                        <x-ez-card class="mb-4">
                            <x-slot name="title">Child seats</x-slot>
                            <x-slot name="body">

                                @forelse($this->stepTwoFields['seats'] as $seat)
                                    <div class="grid grid-cols-4 gap-4">
                                        <div class="col-span-2">
                                            <x-form.ez-select :showEmptyValue="false"
                                                              :model="'stepTwoFields.seats.'.$loop->index"
                                                              :label="'Seat #'.$loop->index +1 .':'"
                                                              :items="\App\Models\Transfer::CHILD_SEATS"
                                                              sm="true"></x-form.ez-select>
                                        </div>
                                    </div>

                                @empty
                                    <x-input-alert type="info">No Child seats added. Add one by pressing + icon!
                                    </x-input-alert>

                                @endforelse

                                <div class="flex justify-end gap-4">

                                    <button class="btn btn-outline  btn-sm btn-circle" wire:click="addSeat"><i
                                            class="fas fa-plus"></i></button>
                                    <button class="btn btn-outline  btn-sm btn-circle" wire:click="removeSeat"><i
                                            class="fas fa-minus"></i></button>
                                </div>

                            </x-slot>
                        </x-ez-card>

                    @endif
                @endif

                @if($this->totalPassengers >1)


                    <div>
                        <label
                            class="label cursor-pointer ml-auto justify-end mr-2   mb-1  ">
                                            <span class="label-text mr-2">
                                              <i class="fas fa-users"></i> Define other travellers</span>
                            <input type="checkbox" wire:model="activateOtherTravellersInput" class="checkbox">
                        </label>
                    </div>


                    @if($activateOtherTravellersInput )
                        <x-ez-card>
                            <x-slot name="title">Other traveller details</x-slot>
                            <x-slot name="body">
                                <div class="grid grid-cols-4 gap-4">
                                    @foreach($this->stepTwoFields['otherTravellers'] as $i => $traveler)
                                        <div class="col-span-1">
                                            <x-form.ez-text-input sm label="Title"
                                                                  model="stepTwoFields.otherTravellers.{{$i}}.title"></x-form.ez-text-input>
                                        </div>
                                        <div class="col-span-1">
                                            <x-form.ez-text-input sm label="First name"
                                                                  model="stepTwoFields.otherTravellers.{{$i}}.firstName"
                                            ></x-form.ez-text-input>
                                        </div>
                                        <div class="col-span-1">
                                            <x-form.ez-text-input sm label="Last name"
                                                                  model="stepTwoFields.otherTravellers.{{$i}}.lastName"
                                            ></x-form.ez-text-input>
                                        </div>

                                        <div class="col-span-1">
                                            <x-form.ez-text-input sm label="Comment"
                                                                  model="stepTwoFields.otherTravellers.{{$i}}.comment"
                                            ></x-form.ez-text-input>
                                        </div>

                                    @endforeach

                                </div>


                            </x-slot>
                        </x-ez-card>

                    @endif
                @endif
            </div>

        @endif
    </div>
    @if($this->availableTransfers->isNotEmpty() && !empty($stepOneFields['destinationId']) && !empty($stepOneFields['startingPointId']) && !empty($stepOneFields['endingPointId']))
        <div x-data="{open: false}" x-show="open" x-transition
             x-init="setTimeout(() => { open = true })">
            <div class="col-span-1 sticky" style="top: 5vh">
                <div>
                    <x-ez-card>
                        <x-slot name="title">Reservation details</x-slot>
                        <x-slot name="body">

                            <div class="divider my-1    "></div>
                            <div class="res-details">
                                @if($this->selectedStartingPoint)
                                    <p><span>From:</span> <b>{{$this->selectedStartingPoint->name}}</b></p>

                                    @if($this->stepOneFields['pickupAddress'])
                                        <p><span>Address:</span> <b
                                                class="text-right">{{$this->stepOneFields['pickupAddress']}}</b></p>
                                    @endif
                                    <div class="divider my-1    "></div>

                                @endif
                                @if($this->selectedEndingPoint)

                                    <p>To: <b>{{$this->selectedEndingPoint->name}}</b></p>

                                    @if($this->stepOneFields['dropoffAddress'])
                                        <p><span>Address:</span> <b
                                                class="text-right">{{$this->stepOneFields['dropoffAddress']}}</b></p>
                                    @endif
                                    <div class="divider my-1    "></div>

                                @endif
                                @if(!empty($this->stepOneFields['date']))
                                    <p>Date to:
                                        <b>{{\Carbon\Carbon::make($this->stepOneFields['date'])->format('d.m.Y')}}</b>
                                    </p>

                                @endif
                                @if(!empty($this->stepOneFields['time']))
                                    <p>Time to:
                                        <b>{{\Carbon\Carbon::make($this->stepOneFields['time'])->format('H:i')}}</b>
                                    </p>

                                @endif
                                @if(!empty($this->stepOneFields['returnDate']))
                                    <p>Time from:
                                        <b>{{\Carbon\Carbon::make($this->stepOneFields['returnDate'])->format('d.m.Y')}}</b>
                                    </p>

                                @endif
                                @if(!empty($this->stepOneFields['returnTime']))
                                    <p>Time from:
                                        <b>{{\Carbon\Carbon::make($this->stepOneFields['returnTime'])->format('H:i')}}</b>
                                    </p>

                                @endif
                                <p>Passengers: <b>{{$this->totalPassengers}}</b></p>
                                <p>Ticket type: <b>{{$this->roundTrip ? 'Round trip' : 'One way'}}</b></p>


                                @if($this->selectedExtras->isNotEmpty())
                                    <div class="divider my-1"></div>


                                    <p class="font-bold">Extras:</p>

                                    @foreach($this->selectedExtras as $extra)
                                        <p>{{$extra->name}}:
                                            <b>{{\Cknow\Money\Money::EUR($extra->partner->first()?->pivot->price)}}</b>
                                        </p>
                                    @endforeach


                                @endif
                                @if($this->stepTwoFields['seats'])
                                    <div class="divider my-1"></div>
                                    <p class="font-bold">Seats:</p>

                                    @foreach($this->stepTwoFields['seats'] as $seat)
                                        <p>{{\App\Models\Transfer::CHILD_SEATS[$seat]}}
                                        </p>
                                    @endforeach


                                @endif

                            </div>
                            <div class="divider my-1    "></div>

                            @if($this->step === 2 && $this->totalPrice)
                                <div class="alert alert-info alert-sm ">
                                    <div class="text-right ml-auto text-white gap-2 pr-2">
                                        Total price:
                                        <b> {{ \App\Facades\EzMoney::format($this->totalPrice->getAmount()) }}
                                            EUR</b>
                                    </div>
                                </div>
                            @endif

                        </x-slot>
                    </x-ez-card>

                    @if($step === 2 && $resSaved == false)
                        <x-ez-card class="mt-4">
                            <x-slot name="body">
                                <x-form.ez-select label="Confirmation language"
                                                  :items="$this->confirmationLanguagesArray"
                                                  model="stepTwoFields.confirmationLanguage" :show-empty-value="false"
                                                  sm="true"></x-form.ez-select>

                                <x-form.ez-select label="Send Email"
                                                  :items="$this->sendEmailArray"
                                                  model="stepTwoFields.sendMail" :show-empty-value="false"
                                                  sm="true"></x-form.ez-select>

                                <button class="btn btn-large btn-accent rounded-box" wire:click="saveReservation"><span
                                        class="mr-4">Complete reservation</span>
                                    <i class="fas fa-arrow-right float-right"></i></button>
                                @error('*')
                                <x-input-alert type="warning">
                                    {{$message}}
                                </x-input-alert>
                                @enderror
                            </x-slot>
                        </x-ez-card>
                    @endif
                </div>

            </div>
        </div>
    @endif

</div>

<script>
    function app() {
        return {

            init() {


            }
        }
    }
</script>
</div>
