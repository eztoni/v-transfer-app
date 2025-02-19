<?php
use Carbon\Carbon;
?>
<x-mail.layouts.main>

    <x-slot name="head_after">
        <style>
            @font-face {
                font-family: 'font';
                font-style: normal;
                font-weight: normal;

            }

            p {
                font-weight: normal !important;
            }
        </style>

    </x-slot>

    <x-mail.body>
        <x-mail.logo>
            <div class="" style="padding: 3px 25px 0 0; text-align: right ;width: 100%">
                <p style="width: 100%">
                    #{{$reservation->id}}
                </p>

            </div>

        </x-mail.logo>


        <x-mail.row>

            <div class="" style="padding-top: 40px;"></div>

            {{ __('mail.dear')}} <strong> {{ucwords($reservation->leadTraveller?->full_name)}}</strong>,
        </x-mail.row>
        <x-mail.row>

            {{ __('mail.thank_you_message')}}
            <br>

        </x-mail.row>

        <x-mail.divider></x-mail.divider>
        <x-mail.row>
            <p style="font-size: 14px"><b>{{__('mail.reservation_number')}}:</b> #{{$reservation->id}}</p>
            <p style="font-size: 14px"><b>{{__('mail.name')}}:</b> {{ucwords($reservation->leadTraveller?->full_name)}}
            </p>

            @if($reservation->leadTraveller->phone)
                <p style="font-size: 14px"><b>{{__('mail.contact_phone')}}:</b> {{$reservation->leadTraveller->phone}}
                </p>
            @endif

            @if($reservation->otherTravellers)
                <p style="font-size: 14px"><b>{{__('mail.other_travellers')}}:</b>{{ $reservation->getFormattedOtherTravellers() }}</p>

            @endif

            <p style="font-size: 14px"><b>{{__('mail.direction')}}:</b>
                @if( $reservation->returnReservation)
                    @if($reservation->returnReservation->status == 'cancelled' || $reservation->status == 'cancelled')
                        One Way
                    @else
                        Round Trip
                    @endif
                @else
                    One Way
                @endif
            </p>

        </x-mail.row>
        <x-mail.divider></x-mail.divider>


        <x-mail.row>


            <h3 style="font-weight: bold; margin-top: 0; margin-bottom: 1%"> <span
                    style="color: #3498DB; text-decoration: none">
                {{__('mail.transfer_itinerary')}}
                </span>
            </h3>
            @if($reservation->status == 'confirmed' || $reservation->status == 'cancelled')
                <p style="font-size: 14px"><b>{{__('mail.pickup_date')}}: </b> {{$reservation->date_time->format('d.m.Y')}}
                </p>
                @if($reservation->flight_number)
                    <p style="font-size: 14px"><b>{{__('mail.flight_number')}}:</b> {{$reservation->flight_number}}</p>
                @endif
                <p style="font-size: 14px"><b>{{__('mail.flight_time')}}: </b> {{$reservation->date_time->format('H:i')}}


                <p style="font-size: 14px"><b>{{__('mail.pickup_address')}}: </b> {{$reservation->pickup_address}}</p>
                @if($reservation->flight_pickup_time)

                    @if($reservation->is_main == 1 && $reservation->pickupAddress->type == 'accommodation')
                        <p style="font-size: 14px"><b>{{__('mail.flight_pickup_time')}}:</b> {{Carbon::parse($reservation->flight_pickup_time)->format('d.m.Y @ H:i')}}</p>
                    @endif
                @endif
                </p>
                <p style="font-size: 14px"><b>{{__('mail.dropoff_address')}}: </b> {{$reservation->dropoff_address}}</p>
                @if($reservation->transfer?->vehicle?->type)
                    <p style="font-size: 14px"><b>{{__('mail.vehicle_type')}}
                            : </b> {{$reservation->transfer->vehicle->type}}</p>
                @endif

                @if(count($reservation->extras) > 0)
                    <p style="font-size: 14px"><b>{{__('mail.extras')}}
                            : </b> {{$reservation->get_extras_list()}}

                    </p>
                @endif

                @if(count($reservation->child_seats) > 0)
                    <b>{{__('mail.seats')}}
                        : </b>
                    @foreach($reservation->child_seats as $seat)
                        <p style="font-size: 14px">{{\App\Models\Transfer::CHILD_SEATS[$seat]}}</p>
                    @endforeach
                @endif

                @if($reservation->remark)
                    <br/>
                    <p style="font-size: 14px"><b>{{__('mail.remark')}}:</b> {{$reservation->remark}}</p>
                @endif
            @endif

            @if($reservation->status == 'confirmed' && $reservation->returnReservation && $reservation->returnReservation->status == 'confirmed')
                                <p style="margin-top: 20px;">{!! __('mail.please_note_round_trip') !!}</p>
                                <br>
            @endif
            @if($reservation->returnReservation && $reservation->returnReservation->status == 'confirmed')

                <p style="font-size: 14px"><b>{{__('mail.pickup_date')}}
                        : </b> {{$reservation->returnReservation->date_time->format('d.m.Y')}}</p>
                @if($reservation->returnReservation->flight_number)
                    <p style="font-size: 14px"><b>{{__('mail.flight_number')}}
                            :</b> {{$reservation->returnReservation->flight_number}}</p>
                @endif

                    <p style="font-size: 14px"><b>{{__('mail.flight_time')}}
                            : </b> {{$reservation->returnReservation->date_time->format('H:i')}}</p>

                <p style="font-size: 14px"><b>{{__('mail.pickup_address')}}
                        : </b> {{$reservation->returnReservation->pickup_address}}</p>
                    @if($reservation->returnReservation->flight_pickup_time)

                        @php

                        $out = false;

                        $main_reservation =  \App\Models\Reservation::where('round_trip_id',$reservation->returnReservation->id)->get()->first();

                        if($main_reservation){
                            if($main_reservation->pickupAddress->type == 'airport'){
                                $out = true;
                            }
                        }

                        @endphp
                            @if($out)
                                <p style="font-size: 14px"><b>{{__('mail.flight_pickup_time')}}:</b> {{Carbon::parse($reservation->returnReservation->flight_pickup_time)->format('d.m.Y @ H:i')}}</p>
                            @endif
                    @endif

                <p style="font-size: 14px"><b>{{__('mail.dropoff_address')}}
                        : </b> {{$reservation->returnReservation->dropoff_address}}</p>
                    @if($reservation->returnReservation->transfer?->vehicle?->type)
                        <p style="font-size: 14px"><b>{{__('mail.vehicle_type')}}
                                : </b> {{$reservation->returnReservation->transfer->vehicle->type}}</p>
                    @endif

                    @if(count($reservation->returnReservation->extras) > 0)
                        <p style="font-size: 14px"><b>{{__('mail.extras')}}
                                : </b> {{$reservation->returnReservation->get_extras_list()}}

                        </p>
                    @endif

                    @if(count($reservation->returnReservation->child_seats) > 0)
                        <b>{{__('mail.seats')}}
                            : </b>
                        @foreach($reservation->returnReservation->child_seats as $seat)
                            <p style="font-size: 14px">{{\App\Models\Transfer::CHILD_SEATS[$seat]}}</p>
                        @endforeach
                    @endif

                    @if($reservation->returnReservation->remark)
                        <br/>
                        <p style="font-size: 14px"><b>{{__('mail.remark')}}:</b> {{$reservation->returnReservation->remark}}</p>
                    @endif
            @endif

        </x-mail.row>
        <x-mail.divider></x-mail.divider>
        <x-mail.row>
            <div>

                @if($reservation->adults)
                    <span
                        style="font-size: 14px;margin-right: 3px;">{{__('mail.adults')}}: <b>{{$reservation->adults}}</b> |</span>
                @endif
                @if($reservation->children)
                    <span
                        style="font-size: 14px;margin-right: 3px">{{__('mail.children')}}: <b>{{$reservation->children}}</b> |</span>
                @endif
                @if($reservation->infants)
                    <span
                        style="font-size: 14px;margin-right: 3px">{{__('mail.infants')}}: <b>{{$reservation->infants}}</b> |</span>
                @endif

                <span
                    style="font-size: 14px;margin-right: 3px">{{__('mail.luggage')}}: <b>{{$reservation->luggage}}</b></span>
            </div>
        </x-mail.row>


        @if($reservation->included_in_accommodation_reservation)
            <x-mail.divider></x-mail.divider>
            <x-mail.row>

                <h3 style="font-weight: bold; margin-top: 5px; margin-bottom: 1%"> <span
                        style="color: #3498DB; text-decoration: none;">
                    {{__('mail.included_in_accommodation_reservation')}}</span>
                </h3>
            </x-mail.row>

        @endif




        @if($reservation->partner->terms)
            <x-mail.divider></x-mail.divider>

            <x-mail.row>
                {!!  nl2br($reservation->partner->terms)!!}
                <br>
                <br>
            </x-mail.row>
        @endif
        <x-mail.row>
            <div class="" style="padding-top: 40px;"></div>
            <p style="margin-bottom:15px;text-align: right">1/2</p>

        </x-mail.row>

    </x-mail.body>


    <div class="page-break"></div>
    <x-mail.body>
        <x-mail.logo></x-mail.logo>

        <x-mail.row>
            <style>
                li {

                    list-style-type: disc;
                    margin:5px;
                }

                ul > li{
                    padding-left:10px;
                }

                ul{
                    padding-left:10px;
                }
            </style>

            <br>
            <br>

            <b>{{__('mail.important_notes')}}</b>
            <p style="font-weight: normal!important;">{{__('mail.important_note_1')}}</p>
            <br/>

            <ul>
                <li  style="list-style-type: none;">1. {{__('mail.pickup_and_meeting_point')}}:
                    <ul style="margin-bottom:2%">
                        <li style="list-style-type: none;margin-bottom:2%"><b>1.1.</b> {{__('mail.li_1_1')}} <b>{{$reservation->partner->email}} {{$reservation->partner->phone}}</b></li>
                        <li  style="list-style-type: none;margin-bottom:2%"><b>1.2.</b> {{__('mail.li_1_2')}}</li>
                        <li style="list-style-type: none;margin-bottom:2%"><b>1.3.</b> {{__('mail.li_1_3')}}</li>
                        <li  style="list-style-type: none;margin-bottom:2%"><b>1.4.</b> {{__('mail.li_2')}} <b style="font-weight: 700;">transfer@valamar.com   +385 (0)52 465 000</b></li>
                    </ul>

                </li>
                <br/>
                <li  style="list-style-type: none;" ><b>2. </b> {{__('mail.li_3')}}:
                    <ul>
                        <li  style="list-style-type: none;"><b>2.1. </b>{{__('mail.li_3_1')}}</li>
                        <li  style="list-style-type: none;"><b>2.2. </b>{{__('mail.li_3_2')}}</li>
                    </ul>
                </li>
            </ul>
            <p style="margin-bottom:15px;text-align: right">2/2</p>


        </x-mail.row>

        <x-mail.footer>

        </x-mail.footer>
    </x-mail.body>

</x-mail.layouts.main>
