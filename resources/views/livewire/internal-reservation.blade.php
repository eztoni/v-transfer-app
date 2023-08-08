

<div class="internal-reservation container " x-data="app()">

    <div class="grid grid-cols-3 gap-4">
        <div class="col-span-2 ">

            @if($step === 1)

                <x-card>
                    <x-slot name="action">
                        <x-button sm label="Pull data"  wire:click="$set('pullModal',true)"
                                  icon="cloud-download"></x-button>
                    </x-slot>

                    <x-modal.card max-width="8xl" wire:model="pullModal" lg title="Pull data from Opera">
                        @if($this->pullModal)
                            <div class="flex gap-4   flex-wrap">
                                <x-input
                                    wire:model.defer="pullDataFields.resId"
                                    label="Accomodation Reservation Number"
                                />

                                <x-input
                                    wire:model.defer="pullDataFields.fName"
                                    label="Reservation Holder Name"/>
                                <x-input
                                    wire:model.defer="pullDataFields.lName"
                                    label="Reservation Holder Last Name"/>

                                    <x-flatpickr
                                        label="Check in:"
                                        min-date="today"
                                        date-format="d.m.Y"
                                        :enable-time="false"
                                        :default-date="$this->pullDataFields['dFrom']"
                                        wire:model="pullDataFields.dFrom"
                                    />

                                    <x-flatpickr
                                        label="Check out:"
                                         min-date="today"
                                        :enable-time="false"
                                        date-format="d.m.Y"
                                        :default-date="$this->pullDataFields['dTo']"
                                        wire:model="pullDataFields.dTo"
                                    />
                                <x-select
                                    option-key-value
                                    :searchable="true"
                                    min-items-for-search="2"
                                    class="w-80"
                                    wire:model.defer="pullDataFields.property"
                                    :options="$this->pointsAccomodation->pluck('name','pms_code')->mapWithKeys(fn($i,$k) =>[$k=>$k.' - '.$i])->toArray()"
                                    label="Property"
                                />
                            </div>
                        @endif

                        <hr class="my-4">
                        @if($this->apiData)


                            <div class="max-h-96 overflow-y-scroll">
                                <table class="ds-table ds-table-compact w-full  ">
                                    <thead>
                                    <tr>
                                        <th>#Res. Code</th>
                                        <th>#Opera ID</th>
                                        <th>#Opera Conf.</th>
                                        <th>RatePlan</th>
                                        <th>First Name</th>
                                        <th>Lastname</th>
                                        <th>Email</th>
                                        <th>Adults</th>
                                        <th>Children</th>
                                        <th>Check in</th>
                                        <th>Check out</th>
                                        <th>Status</th>
                                        <th>Pull</th>

                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($this->apiData as $k=> $r)

                                        <tr>
                                            <th>{{$k}}</th>

                                            <th>{{\Illuminate\Support\Arr::get($r,'OPERA.RESV_NAME_ID')}}</th>
                                            <th>{{\Illuminate\Support\Arr::get($r,'OPERA.CONFIRMATION_NO')}}</th>
                                            <th>{{\Illuminate\Support\Arr::get($r,'rateCode')}}</th>
                                            <th>{{\Illuminate\Support\Str::title( \Illuminate\Support\Arr::get($r,'reservationHolderData.firstName')??'-')}}</th>
                                            <th>{{\Illuminate\Support\Str::title(\Illuminate\Support\Arr::get($r,'reservationHolderData.lastName')??'-')}}</th>
                                            <th>{{\Illuminate\Support\Arr::get($r,'reservationHolderData.email')??'-'}}</th>
                                            <th>{{\Illuminate\Support\Arr::get($r,'adults')}}</th>
                                            <th>{{\Illuminate\Support\Arr::get($r,'children')}}</th>
                                            <th>{{\Carbon\Carbon::parse(\Illuminate\Support\Arr::get($r,'checkIn'))->format('d.m.Y')}}</th>
                                            <th>{{\Carbon\Carbon::parse(\Illuminate\Support\Arr::get($r,'checkOut'))->format('d.m.Y')}}</th>
                                            <th>{{\Illuminate\Support\Arr::get($r,'status')}}</th>
                                            <td>
                                                @if(\Illuminate\Support\Arr::get($r,'status') == 'CANCEL')
                                                    <x-button.circle sm negative disabled wire:click=""
                                                                     icon="cloud-download"/>
                                                @else
                                                <x-button.circle sm positive wire:click="pullRes('{{$k}}')"
                                                                 icon="cloud-download"/>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <div>
                                <div wire:loading.delay class="text-primary">
                                    Loading data...
                                </div>
                            </div>

                            <div>
                                <x-button wire:click="pullData" class="pull-right mt-4  mx-4" primary>Search</x-button>

                                <x-button wire:click="closePullModal" class="pull-right mt-4 ">Close</x-button>

                            </div>


                        </div>

                    </x-modal.card>



                    <div class="grid grid-cols-2 gap-2">
                        <div class="ds-form-control ">
                            @if(!empty($this->stepOneFields['destinationId']))
                                @if($this->startingPoints->isNotEmpty())

                                    <x-native-select
                                        label="Pickup location:"
                                        wire:model="stepOneFields.startingPointId"
                                        :options="$this->startingPoints->prepend(['internal_name'=>'Select a starting point','id'=>''])->pluck('internal_name','id')"
                                        option-key-value
                                    />
                                @else
                                    <div class="ds-alert ds-alert-warning">No Pickup points for that destination</div>
                                @endif
                            @endif
                            @if($this->stepOneFields['startingPointId'] && $this->stepOneFields['endingPointId'] )

                                <div class="ds-form-control  pt-2">
                                    <x-dynamic-component
                                        :component="WireUi::component('label')"
                                        class="mb-1"
                                        label="Pickup address"
                                        :has-error="$this->getErrorBag()->has('stepOneFields.pickupAddress')"
                                        :for="\Illuminate\Support\Str::random()"
                                    />
                                    <div class="ds-form-control" wire:ignore>
                                        <select id="pickupSelect" x-init=" $(' #pickupSelect').select2(
                                        {
                                        closeOnSelect: true,
                                        tags: true,
                                        placeholder: 'Select or type pickup address',
                                        }
                                        ).on('change', function (e) {
                                          $wire.setPickupAddress($('#pickupSelect').val())
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
                                                    value="{{$pickupAddressPoint->id}}">{{$pickupAddressPoint->internal_name}}</option>

                                            @endforeach

                                            @if($itemSelected === false)
                                                <option
                                                    value="{{$this->stepOneFields['pickupAddress']}}" selected>
                                                    {{$this->stepOneFields['pickupAddress']}}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <x-dynamic-component
                                    :component="WireUi::component('error')"
                                    name="stepOneFields.pickupAddress"
                                />
                            @endif
                        </div>
                        @if(!empty($this->stepOneFields['startingPointId']))

                            <div class="ds-form-control ">

                                @if($this->endingPoints->isNotEmpty())
                                    <x-native-select
                                        label="Drop off location:"
                                        wire:model="stepOneFields.endingPointId"
                                        :options="$this->endingPoints->prepend(['internal_name'=>'Select an ending point','id'=>''])->pluck('internal_name','id')"
                                        option-key-value

                                    />
                                @else
                                    <div class="ds-alert ds-alert-warning">No Dropoff points for that pickup point!
                                    </div>

                                @endif
                                @if($this->stepOneFields['startingPointId'] && $this->stepOneFields['endingPointId']  )
                                    <div class="ds-form-control pt-2" >
                                        <x-dynamic-component
                                            :component="WireUi::component('label')"
                                            class="mb-1"
                                            label="Dropoff address"
                                            :has-error="$this->getErrorBag()->has('stepOneFields.dropoffAddress')"
                                            :for="\Illuminate\Support\Str::random()"
                                        />
                                        <div class="ds-form-control">

                                        <select id="dropoffSelect"  wire:key="{{ now() }}" x-init=" $('#dropoffSelect').select2(
                                                {
                                                    closeOnSelect: true,
                                                    tags: true,
                                                      placeholder: 'Select or type dropoff address',
                                                }
                                            ).on('change', function (e) {
                                                 $wire.setDropoffAddress($('#dropoffSelect').val())
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
                                                    value="{{$dropoffAddressPoint->id}}">{{$dropoffAddressPoint->internal_name}}</option>
                                            @endforeach
                                            @if($itemSelected === false)
                                                <option
                                                    value="{{$this->stepOneFields['dropoffAddress']}}" selected>
                                                    {{$this->stepOneFields['dropoffAddress']}}</option>
                                            @endif
                                        </select>

                                    </div>
                                    </div>
                                    <x-dynamic-component
                                        :component="WireUi::component('error')"
                                        name="stepOneFields.dropoffAddress"
                                    />
                                @endif
                            </div>
                        @endif
                    </div>


                    @if(!empty($stepOneFields['destinationId']) && !empty($stepOneFields['startingPointId']) && !empty($stepOneFields['endingPointId']))
                        <div >


                            <div class="ds-divider my-1 "></div>




                            <div class="grid grid-cols-2  gap-2">
                                <x-flatpickr
                                    label="Date to:"
                                    :default-date="$this->stepOneFields['dateTime']"
                                    wire:model.defer="stepOneFields.dateTime"
                                ></x-flatpickr>
                                @if($roundTrip)

                                    <x-flatpickr
                                        label="Date from:"
                                        :default-date="$this->stepOneFields['returnDateTime']"
                                        wire:model.defer="stepOneFields.returnDateTime"
                                    ></x-flatpickr>

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
                                         @focusout="$el.value =='' ? $el.value = this.oldVal:'';$el.dispatchEvent(new Event('input'))"
                                         wire:model="stepOneFields.adults"
                                />
                                <x-input label="Child(3-17):"
                                         x-data="{oldVal:''}"
                                         @focusin="this.oldVal = $el.value;$el.value = ''"
                                         @focusout="$el.value =='' ? $el.value = this.oldVal:'';$el.dispatchEvent(new Event('input'))"
                                         wire:model="stepOneFields.children"
                                />
                                <x-input label="Infant(0-2):"
                                         x-data="{oldVal:''}"
                                         @focusin="this.oldVal = $el.value;$el.value = ''"
                                         @focusout="$el.value =='' ? $el.value = this.oldVal:'';$el.dispatchEvent(new Event('input'))"
                                         wire:model="stepOneFields.infants"
                                />
                                <x-input label="Luggage"
                                         x-data="{oldVal:''}"
                                         @focusin="this.oldVal = $el.value;$el.value = ''"
                                         @focusout="$el.value =='' ? $el.value = this.oldVal:'';$el.dispatchEvent(new Event('input'))"
                                         wire:model="stepOneFields.luggage"
                                />

                                <x-input label="Rate Plan"
                                         wire:model="stepOneFields.rate_plan"
                                />

                            </div>

                        </div>
                    @endif

                </x-card>


                @if($this->availableTransfers->isEmpty() &&
                    !empty($stepOneFields['destinationId']) &&
                    !empty($stepOneFields['startingPointId']) &&
                    !empty($stepOneFields['endingPointId']))
                    <div class="ds-divider"></div>

                    <div >

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
                    <div >

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
                        <x-button wire:click="goBack" label="<< Back"/>
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



                        <x-input label="First name"
                                 wire:model="stepTwoFields.leadTraveller.firstName"
                        ></x-input>
                        <x-input label="Last name"
                                 wire:model="stepTwoFields.leadTraveller.lastName"
                        ></x-input>

                        <x-input label="Reservation number"
                                 wire:model="stepTwoFields.leadTraveller.reservationNumber"
                        ></x-input>

                        <x-input label="Reservation Opera ID"
                                 wire:model="stepTwoFields.leadTraveller.reservationOperaID"
                        ></x-input>

                        <x-input label="Opera Confirmation Number"
                                 wire:model="stepTwoFields.leadTraveller.reservationOperaConfirmation"
                        ></x-input>
                        <x-input label="Email"
                                 wire:model="stepTwoFields.leadTraveller.email"
                        ></x-input>
                        <x-input label="Phone"
                                 wire:model="stepTwoFields.leadTraveller.phone"
                        ></x-input>
                        <x-flatpickr
                            label="Check In:"
                            min-date=""
                            date-format="d.m.Y"
                            :enable-time="false"
                            wire:model.defer="stepTwoFields.leadTraveller.check_in"
                        />

                        <x-flatpickr
                            label="Check Out"
                            min-date=""
                            date-format="d.m.Y"
                            :enable-time="false"
                            wire:model.defer="stepTwoFields.leadTraveller.check_out"
                        />

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
        <div >
            <div class="col-span-1 sticky" style="top: 5vh">
                <div class="flex flex-col gap-4">
                    @if(config('valamar.ez_dev_tools') && Auth::user()->hasRole('super-admin') && !App::isProduction() )
                    <div x-data="{hideDevTools: false}"
                         x-init="hideDevTools = !!localStorage.getItem('hide-res-dev-tools') "
                         x-cloak
                         x-show="!hideDevTools"
                    >
                        <x-card title="Internal dev tools" >

                        <div class="absolute top-2 right-2">
                            <x-button.circle
                                icon="x"
                                @click="localStorage.setItem('hide-res-dev-tools', true); hideDevTools = localStorage.getItem('hide-res-dev-tools');"
                                              sm />
                        </div>

                    <x-toggle wire:model="devRoundTrip" label="Populate round trip"></x-toggle>
                    <hr class="my-4">
                    @foreach($this->populateReservationModes as $mode)
                            <x-button
                                label="{{\Illuminate\Support\Str::upper($mode)}}"
                                wire:click="devPopulateReservation('{{$mode}}')"
                                      sm
                                      primary
                            ></x-button>
                        @endforeach



                    </x-card>
                    </div>
                    @endif
                    @if($this->availableTransfers->isNotEmpty() && !empty($stepOneFields['destinationId']) && !empty($stepOneFields['startingPointId']) && !empty($stepOneFields['endingPointId']))

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
                            @if(!empty($this->stepOneFields['dateTime']))
                                <p>Date:
                                    <b>{{\Carbon\Carbon::make($this->stepOneFields['dateTime'])->format('d.m.Y @ H:i')}}</b>
                                </p>

                            @endif

                            @if(!empty($this->stepOneFields['returnDateTime']) && $this->roundTrip)
                                <p>Return date:
                                    <b>{{\Carbon\Carbon::make($this->stepOneFields['returnDateTime'])->format('d.m.Y @ H:i')}}</b>
                                </p>
                            @endif

                            <p>Passengers: <b>{{$this->totalPassengers}}</b></p>
                            <p>Ticket type: <b>{{$this->roundTrip ? 'Round trip' : 'One way'}}</b></p>

                                @if(!empty($this->stepOneFields['rate_plan']))
                                    <p>Rate Plan:
                                        <b>{{$this->stepOneFields['rate_plan']}}</b>
                                    </p>
                                @endif


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
                                    <?php
                                        if($this->roundTrip){
                                            $multiplier = 2;
                                        }else{
                                            $multiplier = 1;
                                        }
                                        ?>
                                    <b> {{ \App\Facades\EzMoney::format($this->totalPrice->getAmount()*$multiplier) }}
                                        EUR</b>
                                </div>
                            </x-slot>
                        @endif
                    </x-card>


                    @if($step === 1)
                        <div >
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

                                <x-toggle
                                    lg
                                    wire:model.defer="stepTwoFields.includedInAccommodationReservation"
                                label="Price Included in Accommodation Reservation"
                                ></x-toggle><br/>
                                <x-toggle
                                    lg
                                    wire:model.defer="stepTwoFields.vlevelrateplanReservation"
                                label="V Level Rate Plan Reservation"
                                ></x-toggle>
                            <hr class="my-4">
                                <x-select
                                    label="Confirmation language"
                                    wire:model="stepTwoFields.confirmationLanguage"
                                    :options="$this->confirmationLanguagesArray"
                                    option-key-value
                                    :clearable="false"
                                />
                                <div class="my-4 flex justify-end">

                                    <x-checkbox lg
                                                label="Send Email"
                                                wire:model="stepTwoFields.sendMail"
                                    />
                                </div>

                                <x-slot name="footer" class="mt-4">
                                    <x-button wire:click="saveReservation" lg positive class="float-right w-full"
                                              label="{{$completeReservation}}">
                                    </x-button>
                                </x-slot>


                                <x-errors/>
                            </x-card>
                        </div>



                    @endif

                    @endif
                </div>

            </div>
        </div>

    @if($reservationStatusModal)
        <x-modal.card wire:model="reservationStatusModal"  title="Reservation Save Breakdown #{{$this->reservationStatus->id}}">
            <livewire:show-reservation-status  :reservation="$this->reservationStatus"/>
        </x-modal.card>
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
