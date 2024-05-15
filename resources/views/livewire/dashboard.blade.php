<?php

?>
<div>

    @if($this->has_errors())

        @if(!empty($this->get_opera_error_bookings()))
        <div class="mb-4">
            <x-card title="Booking errors overview">
                <div class="mb-4">
                    <x-badge negative outline>

                        <x-icon name="exclamation" class="w-12 h-4" >

                        </x-icon>
                        <div class="w-full">
                            Opera Sync Errors
                            <br>
                            The following bookings below could not be synced with opera, as they are missing the critical information.
                        </div>

                    </x-badge>
                </div>
                @if(!empty($this->get_opera_error_bookings()))
                    @foreach($this->get_opera_error_bookings() as $reservation)


                        @if(is_array($reservation))
                            @continue;
                        @endif

                    <x-card cardClasses="mb-4 border" title="Transfer #{{$reservation->id}}">
                        <x-slot name="action">
                            <div class="flex gap-4 items-center">
                                {{--                        Div below is used to compile these dynamic classes    --}}
                                <span class="ds-badge-primary ds-badge-info ds-badge-warning ds-badge-accent hidden"></span>
                                <span class="ds-badge sm ds-badge-{{$reservation->isRoundTrip()?'accent':'primary'}}">{{$reservation->isRoundTrip()?'Round trip':'One way'}}</span>
                                <span class="ds-badge ds-badge-{{$reservation->getOverallReservationStatus() == 'confirmed' ? 'success':'error'}}">{{ucfirst($reservation->getOverallReservationStatus())}}</span>
                                @if($reservation->included_in_accommodation_reservation == 1)
                                    <span class="font-extrabold text-info">€ 0,00</span>
                                @else
                                    <span class="font-extrabold text-info">{{$reservation->getDisplayPrice()}}</span>
                                @endif

                                <x-button sm icon="external-link" target="_blank" href="{{route('reservation-details',$reservation->id)}}">View</x-button>
                                <x-button positive xs icon="check" wire:click="openResolveModal({{$reservation->id}})">Mark as Resolved</x-button>
                            </div>
                        </x-slot>

                        <div class="flex flex-col w-full">
                            <div class="flex gap-4 md:flex-row flex-col basis-2/3">
                                <span class="font-extrabold text-info">Lead:</span>
                                <span><i class="text-xs fas fa-user"></i> {{$reservation->leadTraveller?->full_name}}  {{$reservation->leadTraveller?->email}}</span>
                                <span><i class="text-xs fas fa-phone"></i> {{$reservation->leadTraveller?->phone}}</span>
                            </div>
                            <div class="m-0 divider"></div>
                            <div class="flex gap-4 md:flex-row flex-col basis-2/3">
                                <span class="font-extrabold text-info">Passengers: </span> <span>{{$reservation->num_passangers}} |</span>
                                <span class="font-extrabold text-info">Luggage:</span> <span>{{$reservation->luggage}} |</span>

                                <span class="font-extrabold text-info">Pickup Location:</span>
                                <span>{{$reservation->pickupLocation->name}} -{{$reservation->date_time->format('d.m.Y H:i')}} - Address: {{$reservation->pickup_address}}</span>
                            </div>
                        </div>
                        <div class="flex flex-col w-full">
                            <div class="flex">
                                <div class="m-0 divider"></div>
                                @if($reservation->included_in_accommodation_reservation == 1)
                                    <small class="font-extrabold text-success">Transfer included in Accommodation Reservation</small>
                                @elseif($reservation->v_level_reservation == 1)
                                    <small class="font-extrabold text-success">V Level Rate Plan Transfer included in Accommodation Reservation</small>
                                @else
                                    <span class="font-extrabold text-info">Opera Status: <span class="ds-badge sm ds-badge-{{$reservation->isSyncedWithOpera()?'success':'error'}}">{{$reservation->isSyncedWithOpera()?'Synced':'Not Synced'}}</span></span>
                                    <span class="font-extrabold text-info">&nbsp;ZKI: <span class="text-info font-normal">{{!empty($reservation->invoices[0]) ? $reservation->invoices[0]?->zki:'-'}}</span></span>
                                    <span class="font-extrabold text-info">&nbsp;JIR: <span class="text-info font-normal">{{!empty($reservation->invoices[0]) ? $reservation->invoices[0]?->jir:'-'}}</span></span>
                                    <span class="font-extrabold text-info">&nbsp;Invoice: <span class="text-info font-normal">{{!empty($reservation->invoices[0]) ? gmdate('Y').'-'.$reservation->invoices[0]->invoice_id.'/'.$reservation->invoices[0]->invoice_establishment.'/'.$reservation->invoices[0]->invoice_device : '-'}}</span></span>
                                @endif


                            </div>
                        </div>
                    </x-card>
                @endforeach
                @endif
            </x-card>
        </div>
        @endif

        @if(!empty($this->get_fiscalization_error_bookings()))
        <div class="mb-4">
            <x-card title="Booking error overview:">
                <div class="">
                    <x-badge negative outline>

                        <x-icon name="exclamation" class="w-12 h-4" >

                        </x-icon>
                        <div class="w-full">
                            Fiscalization Error Bookings
                            <br>
                            The following bookings below have all the data to be processed in Opera, but do not have JIR or ZKI
                        </div>

                    </x-badge>
                </div>
                @foreach($this->get_fiscalization_error_bookings() as $reservation)

                    @if(is_array($reservation))
                        @continue;
                    @endif;

                    <x-card cardClasses="mb-4 border" title="Transfer #{{$reservation->id}}">
                        <x-slot name="action">
                            <div class="flex gap-4 items-center">
                                {{--                        Div below is used to compile these dynamic classes    --}}
                                <span class="ds-badge-primary ds-badge-info ds-badge-warning ds-badge-accent hidden"></span>
                                <span class="ds-badge sm ds-badge-{{$reservation->isRoundTrip()?'accent':'primary'}}">{{$reservation->isRoundTrip()?'Round trip':'One way'}}</span>
                                <span class="ds-badge ds-badge-{{$reservation->getOverallReservationStatus() == 'confirmed' ? 'success':'error'}}">{{ucfirst($reservation->getOverallReservationStatus())}}</span>
                                @if($reservation->included_in_accommodation_reservation == 1)
                                    <span class="font-extrabold text-info">€ 0,00</span>
                                @else
                                    <span class="font-extrabold text-info">{{$reservation->getDisplayPrice()}}</span>
                                @endif

                                <x-button sm icon="external-link" target="_blank" href="{{route('reservation-details',$reservation->id)}}">View</x-button>
                                <x-button positive xs icon="check" wire:click="openResolveModal({{$reservation->id}})">Mark as Resolved</x-button>
                            </div>
                        </x-slot>

                        <div class="flex flex-col w-full">
                            <div class="flex gap-4 md:flex-row flex-col basis-2/3">
                                <span class="font-extrabold text-info">Lead:</span>
                                <span><i class="text-xs fas fa-user"></i> {{$reservation->leadTraveller?->full_name}}  {{$reservation->leadTraveller?->email}}</span>
                                <span><i class="text-xs fas fa-phone"></i> {{$reservation->leadTraveller?->phone}}</span>
                            </div>
                            <div class="m-0 divider"></div>
                            <div class="flex gap-4 md:flex-row flex-col basis-2/3">
                                <span class="font-extrabold text-info">Passengers: </span> <span>{{$reservation->num_passangers}} |</span>
                                <span class="font-extrabold text-info">Luggage:</span> <span>{{$reservation->luggage}} |</span>

                                <span class="font-extrabold text-info">Pickup Location:</span>
                                <span>{{$reservation->pickupLocation->name}} -{{$reservation->date_time->format('d.m.Y H:i')}} - Address: {{$reservation->pickup_address}}</span>
                            </div>
                        </div>
                        <div class="flex flex-col w-full">
                            <div class="flex">
                                <div class="m-0 divider"></div>
                                @if($reservation->included_in_accommodation_reservation == 1)
                                    <small class="font-extrabold text-success">Transfer included in Accommodation Reservation</small>
                                @elseif($reservation->v_level_reservation == 1)
                                    <small class="font-extrabold text-success">V Level Rate Plan Transfer included in Accommodation Reservation</small>
                                @else
                                    <span class="font-extrabold text-info">Opera Status: <span class="ds-badge sm ds-badge-{{$reservation->isSyncedWithOpera()?'success':'error'}}">{{$reservation->isSyncedWithOpera()?'Synced':'Not Synced'}}</span></span>
                                    <span class="font-extrabold text-info">&nbsp;ZKI: <span class="text-info font-normal">{{!empty($reservation->invoices[0]) ? $reservation->invoices[0]?->zki:'-'}}</span></span>
                                    <span class="font-extrabold text-info">&nbsp;JIR: <span class="text-info font-normal">{{!empty($reservation->invoices[0]) ? $reservation->invoices[0]?->jir:'-'}}</span></span>
                                    <span class="font-extrabold text-info">&nbsp;Invoice: <span class="text-info font-normal">{{!empty($reservation->invoices[0]) ? gmdate('Y').'-'.$reservation->invoices[0]->invoice_id.'/'.$reservation->invoices[0]->invoice_establishment.'/'.$reservation->invoices[0]->invoice_device : '-'}}</span></span>
                                @endif


                            </div>
                        </div>
                    </x-card>
                @endforeach
            </x-card>
        </div>
        @endif

        @if(!empty($this->get_connected_document_error_bookings()))
        <div class="mb-4">
            <x-card title="Booking error overview:">
                <div class="">
                    <x-badge negative outline>

                        <x-icon name="exclamation" class="w-12 h-4" >

                        </x-icon>
                        <div class="w-full">
                            Connected Document Errors ( Vezani Dokument )
                            <br>
                            For the following bookings, connected document ( VezaniDokument ) was not applied to Opera reservation
                        </div>

                    </x-badge>
                </div>
                @foreach($this->get_connected_document_error_bookings() as $reservation)


                    @if(is_array($reservation))
                        @continue;
                    @endif

                    <x-card cardClasses="mb-4 border" title="Transfer #{{$reservation->id}}">
                        <x-slot name="action">
                            <div class="flex gap-4 items-center">
                                {{--                        Div below is used to compile these dynamic classes    --}}
                                <span class="ds-badge-primary ds-badge-info ds-badge-warning ds-badge-accent hidden"></span>
                                <span class="ds-badge sm ds-badge-{{$reservation->isRoundTrip()?'accent':'primary'}}">{{$reservation->isRoundTrip()?'Round trip':'One way'}}</span>
                                <span class="ds-badge ds-badge-{{$reservation->getOverallReservationStatus() == 'confirmed' ? 'success':'error'}}">{{ucfirst($reservation->getOverallReservationStatus())}}</span>
                                @if($reservation->included_in_accommodation_reservation == 1)
                                    <span class="font-extrabold text-info">€ 0,00</span>
                                @else
                                    <span class="font-extrabold text-info">{{$reservation->getDisplayPrice()}}</span>
                                @endif

                                <x-button sm icon="external-link" target="_blank" href="{{route('reservation-details',$reservation->id)}}">View</x-button>
                                <x-button positive xs icon="check" wire:click="openResolveModal({{$reservation->id}})">Mark as Resolved</x-button>
                            </div>
                        </x-slot>

                        <div class="flex flex-col w-full">
                            <div class="flex gap-4 md:flex-row flex-col basis-2/3">
                                <span class="font-extrabold text-info">Lead:</span>
                                <span><i class="text-xs fas fa-user"></i> {{$reservation->leadTraveller?->full_name}}  {{$reservation->leadTraveller?->email}}</span>
                                <span><i class="text-xs fas fa-phone"></i> {{$reservation->leadTraveller?->phone}}</span>
                            </div>
                            <div class="m-0 divider"></div>
                            <div class="flex gap-4 md:flex-row flex-col basis-2/3">
                                <span class="font-extrabold text-info">Passengers: </span> <span>{{$reservation->num_passangers}} |</span>
                                <span class="font-extrabold text-info">Luggage:</span> <span>{{$reservation->luggage}} |</span>

                                <span class="font-extrabold text-info">Pickup Location:</span>
                                <span>{{$reservation->pickupLocation->name}} -{{$reservation->date_time->format('d.m.Y H:i')}} - Address: {{$reservation->pickup_address}}</span>
                            </div>
                        </div>
                        <div class="flex flex-col w-full">
                            <div class="flex">
                                <div class="m-0 divider"></div>
                                @if($reservation->included_in_accommodation_reservation == 1)
                                    <small class="font-extrabold text-success">Transfer included in Accommodation Reservation</small>
                                @elseif($reservation->v_level_reservation == 1)
                                    <small class="font-extrabold text-success">V Level Rate Plan Transfer included in Accommodation Reservation</small>
                                @else
                                    <span class="font-extrabold text-info">Opera Status: <span class="ds-badge sm ds-badge-{{$reservation->isSyncedWithOpera()?'success':'error'}}">{{$reservation->isSyncedWithOpera()?'Synced':'Not Synced'}}</span></span>
                                    <span class="font-extrabold text-info">&nbsp;ZKI: <span class="text-info font-normal">{{!empty($reservation->invoices[0]) ? $reservation->invoices[0]?->zki:'-'}}</span></span>
                                    <span class="font-extrabold text-info">&nbsp;JIR: <span class="text-info font-normal">{{!empty($reservation->invoices[0]) ? $reservation->invoices[0]?->jir:'-'}}</span></span>
                                    <span class="font-extrabold text-info">&nbsp;Invoice: <span class="text-info font-normal">{{!empty($reservation->invoices[0]) ? gmdate('Y').'-'.$reservation->invoices[0]->invoice_id.'/'.$reservation->invoices[0]->invoice_establishment.'/'.$reservation->invoices[0]->invoice_device : '-'}}</span></span>
                                @endif


                            </div>
                        </div>
                    </x-card>
                @endforeach
            </x-card>
        </div>
        @endif
    @else
        <div class="mb-4">
            <x-card title="Booking error overview:">
                <div class="">
                    <x-badge positive outline>

                        <x-icon name="check" class="w-12 h-4" >

                        </x-icon>
                        <div class="w-full">
                            No errors on the bookings.
                            <br>
                            All the bookings have been successfully processed, you can continue enjoying your day!
                        </div>

                    </x-badge>
                </div>
            </x-card>
        </div>
    @endif

    @if($reservationResolveModal)
            <x-modal.card wire:model="reservationResolveModal" title="Mark Reservation as Resolved #{{$this->resolveReservation->id}}">
                <livewire:resolve-reservation :reservation="$this->resolveReservation"/>
            </x-modal.card>
    @endif

</div>


