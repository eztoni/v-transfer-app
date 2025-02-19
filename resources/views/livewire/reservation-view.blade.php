<?php
use Carbon\Carbon;
?>
<div>

    <div class="ds-alert

    @switch($reservation->status)
    @case(\App\Models\Reservation::STATUS_CONFIRMED)
        ds-alert-success
        @break
    @case(\App\Models\Reservation::STATUS_CANCELLED)
        ds-alert-error
        @break
    @case(\App\Models\Reservation::STATUS_PENDING)
        ds-alert-warning

    @break
    @endswitch

        ">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Reservation status: {{$reservation->status}}</span>
        </div>
    </div>

    <div class="flex gap-2 pt-4 ">
        <x-card class=" w-1/2 shadow-none border-secondary relative"
                title="From: {{$reservation->pickupLocation->name}}">
            <div>
                <p><strong>Pickup Address: </strong>{{$reservation->pickup_address}} </p>
                <p><strong>Pickup Date: </strong>{{$reservation->date_time->format('d.m.Y H:i')}} </p>
            </div>
        </x-card>
        @if($reservation->returnReservation)
            <i class="fas fa-exchange-alt self-center text-3xl text-secondary"></i>
        @else
            <i class="fas fa-long-arrow-alt-right self-center text-3xl text-secondary"></i>
        @endif

        <x-card class="w-1/2 shadow-none border-secondary" title="  To: {{$reservation->dropoffLocation->name}}">
            <div>
                <p><strong>Dropoff Address: </strong>{{$reservation->dropoff_address}} </p>
                @if($reservation->returnReservation)
                    <p><strong>Round Trip Pickup
                            Date: </strong>{{$reservation->returnReservation->date_time->format('d.m.Y H:i')}} </p>
                @endif
            </div>
        </x-card>
    </div>
    <div class="ds-divider "></div>


    <!-- TAB RESERVATION -->
    <div class="grid md:grid-cols-2  gap-2 mt-2">

        <div class="col-span-1">
            <x-card cardClasses="h-full" class="mb-4 shadow-none border-none" title="Vehicle details">


                <table class="ds-table ds-table-compact w-full">

                    <tbody>


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

            </x-card>


        </div>
        <div class="col-span-1">
            <x-card cardClasses="h-full" class="mb-4  shadow-none border-none" title="Partner details">
                <table class="ds-table ds-table-compact w-full">

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
            </x-card>
        </div>

        <div class="col-span-1">
            <x-card cardClasses="h-full" class="mb-4 shadow-none border-none" title="Transfer information">


                <table class="ds-table ds-table-compact w-full">

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
                    @if($this->reservation->flight_pickup_time)

                        @php

                        $out = false;

                         if($this->reservation->is_main){
                             if($this->reservation->pickupAddress->type == 'accommodation'){
                                $out = true;
                             }
                         }else{

                                 $main_reservation =  \App\Models\Reservation::where('round_trip_id',$this->reservation->id)->get()->first();

                                 if($main_reservation){

                                     if($main_reservation->pickupAddress->type == 'airport'){
                                         $out = true;
                                     }
                                 }
                         }
                        @endphp

                        @if($out)
                        <tr>
                            <td class="font-bold">Guest Pick up:</td>
                            <td>{{Carbon::parse($this->reservation->flight_pickup_time)->format('d.m.Y @ H:i')}}</td>
                        </tr>
                        @endif
                    @endif
                    <tr>
                        <td class="font-bold">Remark:</td>
                        <td><textarea rows="10" cols="55" readonly >{{$this->reservation->remark}}</textarea></td>
                    </tr>
                    </tbody>
                </table>


            </x-card>
        </div>

        <div class="col-span-1">
            <x-card cardClasses="h-full" cardClasses="h-full" class="mb-4 shadow-none border-none "
                    title="Main traveller">

                <x-slot name="action">
                    <x-button wire:click="openTravellerModal({{$this->reservation->leadTraveller->id}},'leadTraveller')"
                              icon="pencil"/>
                </x-slot>


                <table class="ds-table ds-table-compact w-full">

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
                        <td class="font-bold">Phone:</td>
                        <td>{{$this->reservation->leadTraveller->phone}}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Email:</td>
                        <td>{{$this->reservation->leadTraveller->email}}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Rate Plan Code:</td>
                        <td>{{$this->reservation->rate_plan}}</td>
                    </tr>

                    <tr>
                        <td class="font-bold">Reservation number:</td>
                        <td>{{$this->reservation->leadTraveller->reservation_number}}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Opera ID:</td>
                        <td>{{$this->reservation->leadTraveller->reservation_opera_id}}</td>
                    </tr>

                    <tr>
                        <td class="font-bold">Opera Confirmation Number:</td>
                        <td>{{$this->reservation->leadTraveller->reservation_opera_confirmation}}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Confirmation Language</td>
                        <td>{{$this->reservation->confirmation_language}}</td>
                    </tr>
                    </tbody>
                </table>
                @if($reservation->hasModifications())
                    <x-button xs wire:click="sendAgainModificationDialog" class="float-right">Send Modifications again</x-button>
                @endif

                @if($this->reservation->status === \App\Models\Reservation::STATUS_CONFIRMED && $this->reservation->is_main)
                    <x-button xs wire:click="sendAgainConfirmationDialog" class="float-right">Send confirmation again</x-button>
                @endif

            </x-card>

        </div>
        @if(!empty($reservation->child_seats))
            <div class="col-span-1">
                <x-card cardClasses="h-full" class="mb-4 shadow-none border-none" title="Child seats">


                    <table class="ds-table ds-table-compact w-full">
                        <tbody>
                        @foreach($reservation->child_seats as $seat)
                            <tr>
                                <td class="font-bold">Seat #{{$loop->index+1}}:</td>
                                <td>{{\App\Models\Transfer::CHILD_SEATS[$seat]}}</td>
                            </tr>
                        @endforeach


                        </tbody>
                    </table>


                </x-card>

            </div>

        @endif
        @if($this->otherTravellers->isNotEmpty())

            <div class="col-span-1">

                <x-card cardClasses="h-full" class="mb-4 shadow-none border-none" title="Other travellers">


                    <table class="ds-table ds-table-compact w-full">
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
                                    <x-button wire:click="openTravellerModal({{$otherTraveller->id}},'otherTraveller')"
                                              icon="pencil"
                                    />

                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </x-card>

            </div>
        @endif

        @if($this->reservation->extras->isNotEmpty())

            <div class="col-span-1">

                <x-card cardClasses="h-full" class="mb-4 shadow-none border-none" title="Extras">


                    <table class="ds-table ds-table-compact w-full">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name:</th>
                            <th>Description:</th>
                            <th align="center">Quantity</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($this->reservation->extras as $extra)
                            <tr>
                                <td class="text-info">{{$extra->id}}</td>
                                <td>{{$extra->name}}</td>
                                <td> {{Str::limit($extra->description,60)}}</td>
                                <td align="center">{{$this->reservation->getExtrasQuantity($extra->id)}}</td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </x-card>

            </div>
        @endif
        <div class="col-span-1">

            <x-card cardClasses="h-full" class="mb-4 shadow-none border-none" title="Price breakdown">


                <table class="ds-table ds-table-compact w-full">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Item:</th>
                        <th class="text-right">Amount:</th>
                    </tr>
                    </thead>
                    <tbody>

                    @if($this->reservation->included_in_accommodation_reservation == 0)
                        @foreach($this->reservation->price_breakdown as $pbItem)

                            <tr>
                                <td class="text-info">{{$loop->index+1}}</td>
                                <td>{{\App\Actions\Breakdown\GetPriceBreakdownItemLabel::run($pbItem)}}</td>
                                <td class="text-right"> {{Arr::get($pbItem,'amount.formatted')}}</td>
                            </tr>
                        @endforeach
                    @endif
                    <tr>
                        <td class="text-info"></td>
                        <td class="font-bold text-lg">TOTAL:</td>
                        @if($this->reservation->included_in_accommodation_reservation == 0)
                            <td class="font-bold text-lg text-right"> {{$reservation->getPrice()}}</td>
                        @else
                            <td class="font-bold text-lg text-right"> € 0,00</td>
                        @endif

                    </tr>
                    </tbody>
                </table>
            </x-card>

        </div>
    </div>

    @if($travellerModal)
        <x-modal.card wire:model="travellerModal" title="Update traveller data">

            <x-select
                wire:model="traveller.title"
                label="Title:"
                :options="\App\Models\Reservation::TRAVELLER_TITLES"
                option-key-value
            ></x-select>

            <x-input
                wire:model="traveller.first_name"
                label="First Name:"
            ></x-input>


            <x-input
                wire:model="traveller.last_name"
                label="Last Name:"
            ></x-input>

            @if($this->leadTravellerEdit)

                <x-select
                    wire:model="confirmation_lang"
                    label="Confirmation Language:"
                    :options="\App\Models\Reservation::CONFIRMATION_LANGUAGES"
                    option-key-value
                ></x-select>

                <x-input
                    wire:model.defer="traveller.reservation_number"
                    label="Reservation Number:"
                ></x-input>

                <x-input
                    wire:model.defer="traveller.phone"
                    label="Phone:"
                ></x-input>

                <x-input
                    wire:model.defer="traveller.email"
                    label="Email:"
                ></x-input>
                <x-input
                    wire:model.defer="traveller.reservation_opera_id"
                    label="Reservation opera id:"
                ></x-input>
                <x-input
                    wire:model.defer="traveller.reservation_opera_confirmation"
                    label="Reservation opera confirmation:"
                ></x-input>
            @else
                <x-input
                    wire:model.defer="otherTravellerComment"
                    label="Comment:"
                ></x-input>
            @endif

            <x-slot name="footer" class="mt-4 flex justify-between">
                <x-button wire:click="closeTravellerModal()">Close</x-button>
                <x-button wire:click="saveTravellerData()" positive
                >Update
                </x-button>
            </x-slot>

        </x-modal.card>
    @endif
</div>
