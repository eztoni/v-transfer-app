<div class="internal-reservation container " x-data="app()">
    <div class="grid grid-cols-3 gap-4">
        <div class="col-span-2 ">
            @if($step === 1)

                <x-card>
                        <x-slot name="action">
                            <x-button sm label="Pull data" wire:click="$set('pullModal',true)" icon="cloud-download" ></x-button>
                        </x-slot>
                        <x-modal.card max-width="6xl" wire:model="pullModal" lg title="Pull data from Opera">

                            <div class="flex gap-4   flex-wrap">
                                <x-input
                                    wire:model.defer="pullDataFields.resId"
                                    label="Reservation ID"
                                />

                                <x-input
                                    wire:model.defer="pullDataFields.fName"
                                    label="Guest name"/>
                                <x-input
                                    wire:model.defer="pullDataFields.lName"
                                    label="Guest last name"/>




                                <x-datetime-picker
                                    without-time
                                    label="Check in:"
                                    display-format="DD.MM.YYYY"
                                    wire:model.defer="pullDataFields.dFrom"
                                />
                                <x-datetime-picker
                                    without-time
                                    label="Check out:"
                                    display-format="DD.MM.YYYY"
                                    wire:model.defer="pullDataFields.dTo"
                                />

                                <x-select
                                    options-key-value
                                    :searchable="true"
                                    wire:model.defer=""
                                    :options="$this->pointsAccomodation->pluck('name','id')"
                                    label="Property"
                                >

                                </x-select>
                            </div>


                            <hr class="my-4">
                            @if($this->apiData)


                            <div class="max-h-96 overflow-y-scroll">
                                <table class="ds-table ds-table-compact w-full  ">
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
                                                <x-button.circle sm wire:click="pullRes('{{$k}}')" icon="cloud-download" />
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                            @endif
                            <div class="flex justify-between items-center">
                                <div>
                                    <div wire:loading class="text-primary">
                                        Loading data...
                                    </div>
                                </div>

                                <div>
                                    <x-button wire:click="pullData" class="pull-right mt-4  mx-4" primary>Search</x-button>

                                    <x-button   wire:click="closePullModal" class="pull-right mt-4 ">Close</x-button>

                                </div>


                            </div>

                        </x-modal.card>



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



                </x-card>





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
