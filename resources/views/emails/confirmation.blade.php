<x-mail.layouts.main>
    <x-mail.logo></x-mail.logo>
    <x-mail.body>
        <x-mail.row>

            {{ __('mail.dear')}}, <strong> {{$reservation->leadTraveller?->full_name}}</strong>
        </x-mail.row>
        <x-mail.row>

            {{ __('mail.thank_you_message')}}
            <br>

        </x-mail.row>

        <x-mail.divider></x-mail.divider>
        <x-mail.row>
            <p style="font-size: 14px">{{__('mail.reservation_number')}}: #{{$reservation->id}}</p>
            <p style="font-size: 14px">{{__('mail.name')}}: {{$reservation->leadTraveller?->full_name}}</p>
            @if($reservation->leadTraveller->phone)
            <p style="font-size: 14px">{{__('mail.contact_phone')}}: {{$reservation->leadTraveller->phone}}</p>
            @endif

            <p style="font-size: 14px">{{__('mail.direction')}}:
                <b>{{ $reservation->returnReservation ? 'Round Trip' : 'One Way' }}</b></p>
            <br>

        </x-mail.row>
        <x-mail.divider></x-mail.divider>

        <x-mail.row>
            <br>


            <h3 style="font-weight: bold; margin-top: 0; margin-bottom: 1%"> <span
                    style="color: #3498DB; text-decoration: none">
                {{__('mail.transfer_itinerary')}}
                </span>
            </h3>
            <br>

            <p style="font-size: 14px"><b>{{__('mail.pickup_date')}}: </b> {{$reservation->date_time->format('d.m.Y')}}</p>
            @if($reservation->flight_number)
                <p style="font-size: 14px"><b>{{__('mail.flight_number')}}:</b> {{$reservation->flight_number}}</p>
            @endif
            <p style="font-size: 14px"><b>{{__('mail.pickup_address')}}:   </b> {{$reservation->pickup_address}}</p>
            <p style="font-size: 14px"><b>{{__('mail.pickup_time')}}: </b> {{$reservation->date_time->format('H:i')}}</p>
            <p style="font-size: 14px"><b>{{__('mail.dropoff_address')}}: </b> {{$reservation->dropoff_address}}</p>
            @if($reservation->transfer?->vehicle?->type)
            <p style="font-size: 14px"><b>{{__('mail.vehicle_type')}}: </b> {{$reservation->transfer->vehicle->type}}</p>
            @endif

            @if($reservation->remark)
                <p style="font-size: 14px"><b>{{__('mail.remark')}}:</b> {{$reservation->remark}}</p>
            @endif
            @if($reservation->returnReservation)
                <p style="margin-top: 20px;">{!! __('mail.please_note_round_trip') !!}</p>
            <br>
                <p style="font-size: 14px"><b>{{__('mail.pickup_date')}}: </b> {{$reservation->returnReservation->date_time->format('d.m.Y')}}</p>
                @if($reservation->flight_number)
                    <p style="font-size: 14px"><b>{{__('mail.flight_number')}}:</b> {{$reservation->returnReservation->flight_number}}</p>
                @endif
                <p style="font-size: 14px"><b>{{__('mail.pickup_address')}}:   </b> {{$reservation->returnReservation->pickup_address}}</p>
                <p style="font-size: 14px"><b>{{__('mail.pickup_time')}}: </b> {{$reservation->returnReservation->date_time->format('H:i')}}</p>
                <p style="font-size: 14px"><b>{{__('mail.dropoff_address')}}: </b> {{$reservation->returnReservation->dropoff_address}}</p>



            @endif

            <br>
        </x-mail.row>
        <x-mail.divider></x-mail.divider>
        <x-mail.row>
            <div>

                @if($reservation->adults)
                    <span style="font-size: 14px;margin-right: 15px;">{{__('mail.adults')}}: <b>{{$reservation->adults}}</b></span>
                @endif
                @if($reservation->children)
                    <span style="font-size: 14px;margin-right: 15px">{{__('mail.children')}}: <b>{{$reservation->children}}</b></span>
                @endif
                @if($reservation->infants)
                    <span style="font-size: 14px;margin-right: 15px">{{__('mail.infants')}}: <b>{{$reservation->infants}}</b></span>
                @endif

                <span style="font-size: 14px;margin-right: 15px">{{__('mail.luggage')}}: <b>{{$reservation->luggage}}</b></span>
            </div>
        </x-mail.row>
        <x-mail.divider></x-mail.divider>

        <x-mail.row>
            <br>


            <h3 style="font-weight: bold; margin-top: 0; margin-bottom: 1%"> <span
                    style="color: #3498DB; text-decoration: none;">
                    {{__('mail.transfer_price_breakdown')}}</span>
            </h3>
            <br>

            @foreach($reservation->price_breakdown as $pbItem)
                <p style="font-size: 14px; margin-bottom: 10px;">{{$loop->index+1}}
                    . {{\App\Actions\Breakdown\GetPriceBreakdownItemLabel::run($pbItem)}}
                    : {{Arr::get($pbItem,'amount.formatted')}}</p>
            @endforeach

            <p style="font-size: 18px"><b>{{__('mail.total_price')}}: </b>
                <b>{{\Cknow\Money\Money::EUR($reservation->price)}}</b></p>

            <br>
        </x-mail.row>


        <x-mail.divider></x-mail.divider>

        <x-mail.row>
            {!!  nl2br($reservation->partner->terms)!!}
            <br>
            <br>
        </x-mail.row>
        <x-mail.row>
            <b>{{__('mail.important_notes')}}</b>
            <p>{{__('mail.important_note_1')}}</p>


            <ul>
                <li>{{__('mail.pickup_and_meeting_point')}}:</li>
                <ul>
                    <li>{{__('mail.li_1_1')}} {{$reservation->partner->email}} {{$reservation->partner->phone}}</li>
                    <li>{{__('mail.li_1_2')}}</li>
                    <li>{{__('mail.li_1_3')}}</li>
                </ul>
                <li>{{__('mail.li_2')}} {{$reservation->partner->email}} {{$reservation->partner->phone}}</li>
                <li>{{__('mail.li_3')}}:</li>
                <ul>
                    <li>{{__('mail.li_3_1')}}:</li>
                    <li>{{__('mail.li_3_2')}}:</li>
                </ul>
            </ul>

        </x-mail.row>



    </x-mail.body>

    <x-mail.footer>
        {{__('mail.valamar_transfer_service')}}
    </x-mail.footer>

    <x-mail.footer-below>
        {{__('mail.not_fiscalized')}}
    </x-mail.footer-below>
</x-mail.layouts.main>
