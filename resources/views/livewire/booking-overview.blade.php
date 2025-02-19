<div>
    <div class="mb-1">
        <x-card title="Booking overview:">
                <div class="flex md:flex-row flex-col gap-4">
                    <div class="flex md:flex-row flex-col flex-warp flex-grow gap-4">
                        <div class="w-20">
                            <x-input wire:model.defer="bookingId"
                                     placeholder="123"

                                     label="Booking ID" />
                            </div>
                            <x-input wire:model.defer="search"
                                     placeholder="Search..."

                                     label="Search"/>
                            <x-select
                                label="Destination"
                                option-key-value
                                :options="$destinations->pluck('name','id')"
                                wire:model.defer="destinationId"
                                      />

                            <x-select
                                label="Partner"
                                option-key-value
                                wire:model.defer="partnerId"
                                :options="$partners->pluck('name','id')"
                            />
                            <x-select
                                label="Object"
                                option-key-value
                                wire:model.defer="pointId"
                                :options="$points->pluck('name','id')"
                            />
                            <x-datetime-picker
                                without-time
                                label="Date from"
                                wire:model.defer="from"
                            />
                            <x-datetime-picker
                                without-time
                                label="Date to"
                                wire:model.defer="to"
                            />

                    </div>
                    <x-button primary wire:click="search">Search</x-button>
                    <x-button success href="{{route('internal-reservation')}}">+ New </x-button>
                </div>
        </x-card>
    </div>

{{--    Pagination --}}
    <div class="mb-4 bg-white">
        <div class="mt-10 mb-10 mr-5 pb-3 pt-3 pl-3 pr-3 flex justify-between items-center">
            @php
                $disabled = '';
                if ($page == 1) {
                    $disabled = 'disabled';
                }
            @endphp

                <!-- Previous Button -->
            <x-button
                success
                wire:click="previousPage"
                class="px-4 py-2 bg-white"
            >
                Previous
            </x-button>

            <!-- Page Info and Year Selector -->
            <div class="flex items-center space-x-4">
            <span class="whitespace-nowrap">
                Latest reservations for <b>{{ \App\Models\Destination::findOrFail(Auth::user()->destination_id)->name }}&nbsp;</b>
            </span>

                <!-- Display Year Dropdown (Aligned to Right of Label) -->
                <x-select
                    :options="array_combine(range(now()->year - 1, now()->year), range(now()->year - 1, now()->year))"
                    wire:model="selectYear"
                    :selected="now()->year"
                    class="w-36"
                />
            </div>

            @php
                $disabled = '';
                if ($page == ceil($totalReservations / $perPage)) {
                    $disabled = 'disabled';
                }
            @endphp

                <!-- Next Button -->
            <x-button
                wire:click="nextPage"
                class="px-4 py-2 bg-white"
            >
                Next
            </x-button>
        </div>
    </div>




@foreach($this->reservations as $reservation)

        <x-card cardClasses="mb-4" title="Transfer #{{$reservation->id}}">
                <x-slot name="action">
                    <div class="flex gap-4 items-center">
{{--                        Div below is used to compile these dynamic classes    --}}
                    <span class="ds-badge-primary ds-badge-info ds-badge-warning ds-badge-accent hidden"></span>
                    <span class="ds-badge sm ds-badge-{{$reservation->isTotalRoundTrip()?'accent':'primary'}}">{{$reservation->isTotalRoundTrip()?'Round trip':'One way'}}</span>
                        <span class="ds-badge ds-badge-{{$reservation->getOverallReservationStatus() == 'confirmed' ? 'success':'error'}}">{{ucfirst($reservation->getOverallReservationStatus())}}</span>
                        @if($reservation->included_in_accommodation_reservation == 1)
                            <span class="font-extrabold text-info">€ 0,00</span>
                        @else
                            <span class="font-extrabold text-info">{{$reservation->getDisplayPrice()}}</span>
                        @endif

                        <x-button sm icon="external-link" target="_blank" href="{{route('reservation-details',$reservation->id)}}">View</x-button>
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

</div>
