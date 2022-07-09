<div class="internal-reservation container " x-data="app()">
    <div class="grid grid-cols-3 gap-4">
        <div class="col-span-2 ">
            @if($step === 1)

                <x-card>

                        <x-ez-modal :is-open="$this->pullModal" lg>

                            <div class="flex gap-4  flex-wrap">
                                <x-form.ez-text-input
                                    wire:model.defer="pullDataFields.resId"
                                    sm label="Reservation ID"
                                ></x-form.ez-text-input>

                                <x-form.ez-text-input
                                    wire:model.defer="pullDataFields.fName"
                                    sm label="Guest name"></x-form.ez-text-input>
                                <x-form.ez-text-input
                                    wire:model.defer="pullDataFields.lName"
                                    sm label="Guest last name"></x-form.ez-text-input>

                            </div>
                            <div class="flex gap-4   mt-2 flex-wrap">

                            <div class=""><p class="text-sm">Check in:</p>
                                    <input x-init="
                                        flatpickr($el, {
                                        disableMobile: 'true',
                                        minDate:'today',
                                        dateFormat:'d.m.Y',
                                        defaultDate:'{{$pullDataFields['dFrom']}}'});
                                        " readonly
                                           wire:model.defer="pullDataFields.dFrom"
                                           class=" input input-bordered input-sm mt-2"
                                           placeholder="Date to:">
                                </div>

                                <div class=""><p class="text-sm">Check out:</p>

                            <input x-init="
                                        flatpickr($el, {
                                        disableMobile: 'true',
                                        minDate:'today',
                                        dateFormat:'d.m.Y',
                                        defaultDate:'{{$pullDataFields['dTo']}}'});
                                        " readonly
                                   wire:model.defer="pullDataFields.dTo"
                                   class=" input input-bordered input-sm mt-2"
                                   placeholder="Date to:">


                            </div>

                                <div class="">
                                    <div class="form-control pt-2" wire:ignore>
                                        <label class="label-text mb-1 ">Property</label>

                                        <select id="propertySelect" x-init=" $('#propertySelect').select2(
                                                {
                                                    closeOnSelect: true,

                                                      placeholder: 'Select or type dropoff address',
                                                }
                                            ).on('change', function (e) {
                                                @this.
                                                set('pullDataFields.property', $('#propertySelect').val())
                                            })
                                            ">
                                            <option></option>

                                            @foreach($this->pointsAccomodation as $point)
                                                <option
                                                    @if($this->pullDataFields['property'] === $point->pms_code)
                                                    selected
                                                    @endif
                                                    value="{{$point->pms_code}}">{{$point->name}}</option>
                                            @endforeach

                                        </select>

                                    </div>
                                </div>
                            </div>
                            <hr class="my-4">
                            @if($this->apiData)


                            <div class="max-h-96 overflow-y-scroll">
                                <table class="table table-compact w-full  ">
                                    <thead>
                                    <tr>
                                        <th>#Res. Code</th>
                                        <th>First Name</th>
                                        <th>Lastname</th>
                                        <th>Email</th>
                                        <th>Adults</th>
                                        <th>Children</th>
                                        <th>Check in</th>
                                        <th>Check out</th>
                                        <th>Pull</th>

                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($this->apiData as $k=> $r)

                                        <tr>
                                            <th>{{$k}}</th>
                                            <th>{{\Illuminate\Support\Str::title( \Illuminate\Support\Arr::get($r,'reservationHolderData.firstName')??'-')}}</th>
                                            <th>{{\Illuminate\Support\Str::title(\Illuminate\Support\Arr::get($r,'reservationHolderData.lastName')??'-')}}</th>
                                            <th>{{\Illuminate\Support\Arr::get($r,'reservationHolderData.email')??'-'}}</th>
                                            <th>{{\Illuminate\Support\Arr::get($r,'adults')}}</th>
                                            <th>{{\Illuminate\Support\Arr::get($r,'children')}}</th>
                                            <th>{{\Carbon\Carbon::parse(\Illuminate\Support\Arr::get($r,'checkIn'))->format('d.m.Y')}}</th>
                                            <th>{{\Carbon\Carbon::parse(\Illuminate\Support\Arr::get($r,'checkOut'))->format('d.m.Y')}}</th>

                                            <td>
                                                <button wire:click="pullRes('{{$k}}')" class="btn btn-xs" ><i class="fas fa-download"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                            @endif
                            <div class="flex justify-between items-center">

                                <div>
                                    <label wire:click="pullData" class="pull-right mt-4 btn btn-primary btn-sm btn-outline mx-4">Search</label>

                                    <label   wire:click="closePullModal" class="pull-right mt-4 btn btn-sm btn-outline">Close</label>

                                </div>
                                <div wire:loading.delay class="text-primary">
                                    Loading data...
                                </div>

                            </div>

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
                    <div class="grid grid-cols-2 gap-2">
                        <div class="ds-form-control ">
                            @if(!empty($this->stepOneFields['destinationId']))
                                @if($this->startingPoints->isNotEmpty())

                                    <x-select
                                        label="Pickup location:"
                                        wire:model="stepOneFields.startingPointId"
                                        :options="$this->startingPoints->pluck('name','id')"
                                        option-key-value
                                    />
                                @else
                                    <div class="ds-alert ds-alert-warning">No Pickup points for that destination</div>
                                @endif
                            @endif
                            @if($this->stepOneFields['startingPointId'] && $this->stepOneFields['endingPointId'] )

                                <div class="ds-form-control  pt-2" wire:ignore>
                                    <label class="label-text text-sm">Pickup address</label>
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

                            @endif
                        </div>
                        @if(!empty($this->stepOneFields['startingPointId']))

                            <div class="ds-form-control ">

                                @if($this->endingPoints->isNotEmpty())
                                    <x-select
                                        label="Drop off location:"
                                        wire:model="stepOneFields.endingPointId"
                                        :options="$this->endingPoints->pluck('name','id')"
                                        option-key-value

                                    />
                                @else
                                    <div class="ds-alert ds-alert-warning">No Dropoff points for that pickup point!
                                    </div>

                                @endif
                                @if($this->stepOneFields['startingPointId'] && $this->stepOneFields['endingPointId']  )
                                    <div class="ds-form-control pt-2" wire:ignore>
                                        <label class="label-text text-sm">Dropoff address</label>

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
                                @endif
                            </div>
                        @endif
                    </div>


                    @if(!empty($stepOneFields['destinationId']) && !empty($stepOneFields['startingPointId']) && !empty($stepOneFields['endingPointId']))
                        <div x-data="{open: false}" x-show="open" x-transition
                             x-init="setTimeout(() => { open = true })">


                            <div class="ds-divider my-1 "></div>


                            <div class="grid grid-cols-2  gap-2">
                                <x-datetime-picker
                                    label="Date to:"
                                    wire:model="stepOneFields.dateTime"
                                    :min="now()"
                                    time-format="24"
                                    display-format="DD.MM.YYYY HH:mm"
                                />

                                @if($roundTrip)
                                    <x-datetime-picker
                                        label="Date from:"
                                        wire:model="stepOneFields.returnDateTime"
                                        :min="now()"
                                        time-format="24"
                                        display-format="DD.MM.YYYY HH:mm"
                                    />
                                @endif
                            </div>

                            <div class="flex justify-end my-3">
                                <x-checkbox lg class="justify-end ml-auto" left-label="Round trip"
                                            wire:model="roundTrip"/>
                            </div>


                            <div class="ds-divider my-1 "></div>

                            <div class="flex flex-wrap justify-between gap-2">

                                <x-input label="Adults:"
                                         x-data="{oldVal:''}"
                                         @focusin="this.oldVal = $el.value;$el.value = ''"
                                         @focusout="$el.value =='' ? $el.value = this.oldVal:''"
                                         wire:model="stepOneFields.adults"
                                />
                                <x-input label="Child(3-17):"
                                         x-data="{oldVal:''}"
                                         @focusin="this.oldVal = $el.value;$el.value = ''"
                                         @focusout="$el.value =='' ? $el.value = this.oldVal:''"
                                         wire:model="stepOneFields.children"
                                />
                                <x-input label="Infant(0-2):"
                                         x-data="{oldVal:''}"
                                         @focusin="this.oldVal = $el.value;$el.value = ''"
                                         @focusout="$el.value =='' ? $el.value = this.oldVal:''"
                                         wire:model="stepOneFields.infants"
                                />
                                <x-input label="Luggage"
                                         wire:model="stepOneFields.luggage"
                                />

                            </div>
                        </div>
                    @endif
                </x-card>

                @if($this->availableTransfers->isEmpty() &&
                    !empty($stepOneFields['destinationId']) &&
                    !empty($stepOneFields['startingPointId']) &&
                    !empty($stepOneFields['endingPointId']))


                    <div x-data="{open: false}" x-show="open" x-transition
                         x-init="setTimeout(() => { open = true })">

                        <div class="ds-alert ds-alert-info  shadow-lg ">
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

                        <div class="ds-divider"></div>

                        <div class="my-4">
                            <x-card>
                                <div class="ds-card-title "><i class="fas fa-search text-lg"></i>
                                    Search results:
                                </div>
                            </x-card>
                        </div>


                        @php
                            $lastTransfer = null;
                        @endphp
                        @foreach($this->availableTransfers as $item)

                            {{--  Check if last transfer has the same partner as current one --}}
                            @if(!$lastTransfer ||($lastTransfer && $lastTransfer->partner->id !== $item->partner->id))
                                @if($lastTransfer)
                    </div>
                @endif


                <div
                    class="border-2  gap-2 mb-2 bg-gradient-to-b from-primary-500 to-white pb-2 flex flex-col rounded-lg relative shadow-md">
                    <div class="w-full flex justify-between text-neutral-content px-4 pt-1 text-lg font-bold "
                    >
                        <span>  {{$item->partner->name}}</span>
                        <a class="font-medium link"
                           href="tel:{{$item->partner->phone}}">{{$item->partner->phone}}</a>
                    </div>

                    @endif

                    @php
                        $lastTransfer = $item;
                    @endphp
                    <div class="ds-card rounded-none bg-base-100  ">
                        <div
                            class="ds-card-body p-2 {{$this->isTransferPartnerPairSelected($item->partner_id,$item->transfer_id) ?'shadow-inner bg-blue-100':''}}">

                            <div class="flex gap-4">
                                <div class="basis-1/5">
                                    <img class="h-24 w-full object-cover rounded-xl"
                                         src="{{$item->transfer->primaryImageUrl}}"/>
                                </div>
                                <div class="basis-4/5">
                                    <h2 class="ds-card-title mb-2">{{$item->transfer->name}}</h2>
                                    <div class="flex gap-4 mb-2">
                                        <span class=" ">Type: Van</span>
                                        <span
                                            class=" ">Max. Occ: {{$item->transfer->vehicle->max_occ}}</span>
                                        <span
                                            class=" ">Max. Luggage:{{$item->transfer->vehicle->max_luggage}}</span>

                                    </div>

                                    <span class="  ">Price: <b>
                                     {{\App\Facades\EzMoney::format($this->roundTrip
                                            ?$item->price_round_trip
                                            :$item->price)}}
                                            EUR</b></span>

                                    <x-button
                                        :primary="$this->isTransferPartnerPairSelected($item->partner_id,$item->transfer_id)"
                                        class="absolute bottom-2 right-2"
                                        wire:click="selectTransfer({{$item->transfer_id}},{{$item->partner_id}})">
                                        {{$this->isTransferPartnerPairSelected($item->partner_id,$item->transfer_id) ?'Selected':'Select'}}
                                    </x-button>
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
            <div class="mb-2">
                <x-card>
                    <div class="flex justify-end">
                        <x-button wire:click="goBack" label="Back"/>
                    </div>
                </x-card>
            </div>

            <div class="ds-divider"></div>
            <div class="mb-2">

            <x-card title="Transfer details">
                <div class="grid grid-cols-3 gap-4">

                    <x-textarea
                        label="Remark:"
                        wire:model="stepTwoFields.remark"
                    />

                    <x-input wire:model="stepTwoFields.arrivalFlightNumber"
                             label="Flight number {{$roundTrip?'#1':''}}"
                    ></x-input>

                    @if($roundTrip)
                        <x-input label="Flight number #2"
                                 wire:model="stepTwoFields.departureFlightNumber"
                        ></x-input>
                    @endif
                </div>

            </x-card>
            </div>
            <div class="mb-2">
                <x-card title="Lead traveller details">
                    <div class="grid grid-cols-3 gap-4">

                        <x-select
                            wire:model="stepTwoFields.leadTraveller.title"
                            label="Title"
                            :options="\App\Models\Reservation::TRAVELLER_TITLES"
                            option-key-value
                        />

                        <x-input label="First name"
                                 wire:model="stepTwoFields.leadTraveller.firstName"
                        ></x-input>
                        <x-input label="Last name"
                                 wire:model="stepTwoFields.leadTraveller.lastName"
                        ></x-input>

                        <x-input label="Reservation number"
                                 wire:model="stepTwoFields.leadTraveller.reservationNumber"
                        ></x-input>
                        <x-input label="Email"
                                 wire:model="stepTwoFields.leadTraveller.email"
                        ></x-input>
                        <x-input label="Phone"
                                 wire:model="stepTwoFields.leadTraveller.phone"
                        ></x-input>
                    </div>
                </x-card>

            </div>


            <div class="flex justify-end my-4">

                <x-checkbox
                lg
                wire:model="activateExtras"
                label="Add extras"
                >
                </x-checkbox>


            </div>
            @if($this->activateExtras)
                <div class="mb-4">
                <x-card class="mb-4" title="Extras">
                        @if($this->extras->isNotEmpty())

                            <table class="ds-table w-full">
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
                                                <x-checkbox
                                                    lg
                                                       wire:model="stepTwoFields.extras.{{$extra->id}}"
                                                      />
                                        </th>
                                        <td>
                                            <div class="flex items-center space-x-3">
                                                <div class="ds-avatar">
                                                    <div class="ds-mask ds-mask-squircle w-12 h-12">
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

                </x-card>
                </div>
            @endif

            @if(($stepOneFields['children']>1 || $stepOneFields['infants']>1))

                <div class="flex justify-end my-4">

                    <x-checkbox
                        lg
                        wire:model="activateChildSeats"
                        label="Add child seats"
                    >
                    </x-checkbox>


                </div>

                @if($this->activateChildSeats )
                    <div class="mb-4">
                        <x-card title="Child seats">
                            <div class="grid grid-cols-4 gap-4">

                                @foreach($this->stepTwoFields['seats'] as $seat)
                                    <div class="col-span-2">
                                        <x-select
                                            label="Seat #{{($loop->index +1)}}:"
                                            wire:model="stepTwoFields.seats.{{$loop->index}}"
                                            :options="\App\Models\Transfer::CHILD_SEATS"
                                            option-key-value
                                        />
                                    </div>

                                @endforeach
                            </div>
                            <x-slot name="footer">
                                <div class="flex justify-end gap-4 mt-2">
                                    <x-button.circle icon="minus" wire:click="removeSeat"></x-button.circle>

                                    <x-button.circle secondary icon="plus" wire:click="addSeat"></x-button.circle>
                                </div>
                            </x-slot>
                        </x-card>
                    </div>
                @endif
            @endif

            @if($this->totalPassengers >1)


                <div class="flex justify-end my-4">

                    <x-checkbox
                        lg
                        wire:model="activateOtherTravellersInput"
                        label="Define other travellers"
                    >
                    </x-checkbox>


                </div>


                @if($activateOtherTravellersInput )
                    <x-card title="Other traveller details">
                        <div class="grid grid-cols-4 gap-4">
                            @foreach($this->stepTwoFields['otherTravellers'] as $i => $traveler)
                                <div class="col-span-4">
                                    <p class="font-bold">Traveller #{{$i}}:</p>
                                </div>

                                <x-select
                                    wire:model="stepTwoFields.otherTravellers.{{$i}}.title"
                                    label="Title"
                                    option-key-value
                                    :options="\App\Models\Reservation::TRAVELLER_TITLES"
                                ></x-select>
                                <x-input label="First name"
                                         wire:model="stepTwoFields.otherTravellers.{{$i}}.firstName"
                                ></x-input>
                                <x-input label="Last name"
                                         wire:model="stepTwoFields.otherTravellers.{{$i}}.lastName"
                                ></x-input>

                                <x-input label="Comment"
                                         wire:model="stepTwoFields.otherTravellers.{{$i}}.comment"
                                ></x-input>

                            @endforeach

                        </div>
                    </x-card>
                @endif
            @endif

        @endif
    </div>
    @if($this->availableTransfers->isNotEmpty() && !empty($stepOneFields['destinationId']) && !empty($stepOneFields['startingPointId']) && !empty($stepOneFields['endingPointId']))
        <div x-data="{open: false}" x-show="open" x-transition
             x-init="setTimeout(() => { open = true })">
            <div class="col-span-1 sticky" style="top: 5vh">
                <div>

                    <x-card title="Reservation details">
                        <div class="res-details">
                            @if($this->selectedStartingPoint)
                                <p><span>From:</span> <b>{{$this->selectedStartingPoint->name}}</b></p>

                                @if($this->stepOneFields['pickupAddress'])
                                    <p><span>Address:</span> <b
                                            class="text-right">{{$this->stepOneFields['pickupAddress']}}</b></p>
                                @endif
                                <div class="ds-divider my-1    "></div>

                            @endif
                            @if($this->selectedEndingPoint)

                                <p>To: <b>{{$this->selectedEndingPoint->name}}</b></p>

                                @if($this->stepOneFields['dropoffAddress'])
                                    <p><span>Address:</span> <b
                                            class="text-right">{{$this->stepOneFields['dropoffAddress']}}</b></p>
                                @endif
                                <div class="ds-divider my-1    "></div>

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
                                <div class="ds-divider my-1"></div>


                                <p class="font-bold">Extras:</p>

                                @foreach($this->selectedExtras as $extra)
                                    <p>{{$extra->name}}:
                                        <b>{{\Cknow\Money\Money::EUR($extra->partner->first()?->pivot->price)}}</b>
                                    </p>
                                @endforeach


                            @endif
                            @if($this->stepTwoFields['seats'])
                                <div class="ds-divider my-1"></div>
                                <p class="font-bold">Seats:</p>

                                @foreach($this->stepTwoFields['seats'] as $seat)
                                    <p>{{\Illuminate\Support\Arr::get(\App\Models\Transfer::CHILD_SEATS,$seat)}}
                                    </p>
                                @endforeach


                            @endif

                        </div>

                        @if($this->step === 2 && $this->totalPrice)

                            <x-slot name="footer">
                                <div class="text-right ml-auto gap-2 pr-2">
                                    Total price:
                                    <b> {{ \App\Facades\EzMoney::format($this->totalPrice->getAmount()) }}
                                        EUR</b>
                                </div>
                            </x-slot>
                        @endif
                    </x-card>


                    @if($step === 1)
                        <div class="my-2 ">
                            <x-card>
                                <x-button lg wire:click="nextStep" class="float-right" right-icon="arrow-right" positive
                                         class="w-full" label="Next step"></x-button>
                            </x-card>
                        </div>

                        <x-errors/>



                    @endif



                    @if($step === 2 && $resSaved == false)
                        <div class="my-2">
                            <x-card>

                                <x-select
                                    label="Confirmation language"
                                    wire:model="stepTwoFields.confirmationLanguage"
                                    :options="$this->confirmationLanguagesArray"
                                    option-key-value
                                />
                                <div class="my-4 flex justify-end">

                                <x-checkbox lg
                                            label="Send Email"
                                            wire:model="stepTwoFields.sendMail"
                                />
                                </div>

                                <x-slot name="footer" class="mt-4">
                                    <x-button wire:click="saveReservation" lg positive class="float-right w-full"
                                              label="Complete reservation">
                                    </x-button>
                                </x-slot>


                                <x-errors/>
                            </x-card>
                        </div>



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
