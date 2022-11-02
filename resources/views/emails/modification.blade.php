<x-mail.layouts.main>
    <x-mail.logo></x-mail.logo>
    <x-mail.body>
        <x-mail.row>
            <div style="font-family:Arial, sans-serif;font-size:14px;line-height:28px;text-align:left;color:#55575d;">
                {{__('mail.dear')}}, {{$reservation->leadTraveller?->full_name}}
            </div>
            <div style="font-family:Arial, sans-serif;font-size:14px;line-height:28px;text-align:left;color:#55575d;">
                <b>{{__('mail.your_res_modified')}}</b><br>
                {{__('mail.this_email_is_modification')}}
            </div>
        </x-mail.row>

        <x-mail.divider></x-mail.divider>
        <x-mail.row>
            <br>

            <h3 style="font-weight: bold; margin-top: 0; margin-bottom: 1%"> <span
                    style="color: #3498DB; text-decoration: none">
                             {{__('mail.transfer_reservation')}}
                </span>
            </h3>
            <br>

            <p style="font-size: 14px">{{__('mail.reservation_number')}}: #{{$reservation->id}}</p>
            <p style="font-size: 14px">{{__('mail.name')}}: {{$reservation->leadTraveller?->full_name}}</p>
            <p style="font-size: 14px">{{__('mail.contact_phone')}}: {{$reservation->leadTraveller?->phone}}</p>
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


            <p style="font-size: 14px"><b>{{__('mail.pickup_address')}}:   </b> {{$reservation->pickup_address}}</p>
            <p style="font-size: 14px"><b>{{__('mail.dropoff_address')}}: </b> {{$reservation->dropoff_address}}</p>
            <p style="font-size: 14px"><b>{{__('mail.pickup_date')}}: </b> {{$reservation->date_time->format('d.m.Y H:i')}}</p>
            @if($reservation->flight_number)
                <p style="font-size: 14px"><b>{{__('mail.flight_number')}}:</b> {{$reservation->flight_number}}</p>
            @endif

            @if($reservation->remark)
                <p style="font-size: 14px"><b>{{__('mail.remark.')}}:</b> {{$reservation->remark}}</p>
            @endif
            @if($reservation->returnReservation)
                <p style="margin-top: 20px;">{{__('mail.please_note_round_trip')}}</p>
                <p style="font-size: 14px"><b>{{__('mail.rt_pickup')}}: </b> {{$reservation->dropoff_address}}</p>
                <p style="font-size: 14px"><b>{{__('mail.rt_dropoff')}}: </b> {{$reservation->pickup_address}}</p>
                <p style="font-size: 14px"><b>{{__('mail.rt_date')}}: </b> {{$reservation->returnReservation->date_time->format('d.m.Y H:i')}}</p>
            @endif

            <br>
        </x-mail.row>
        <x-mail.divider></x-mail.divider>
        <x-mail.row>
            <div>

                @if($reservation->adults)
                    <span style="font-size: 14px;margin-right: 15px;">{{__('mail.adults')}}: {{$reservation->adults}}</span>
                @endif
                @if($reservation->children)
                    <span style="font-size: 14px;margin-right: 15px">{{__('mail.children')}}: {{$reservation->children}}</span>
                @endif
                @if($reservation->infants)
                    <span style="font-size: 14px;margin-right: 15px">{{__('mail.infants')}}: {{$reservation->infants}}</span>
                @endif

                <span style="font-size: 14px;margin-right: 15px">{{__('mail.luggage')}}: {{$reservation->luggage}}</span>
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


    </x-mail.body>

    <x-mail.footer>
        {{__('mail.valamar_transfer_service')}}
    </x-mail.footer>

    <x-mail.footer-below>
        {{__('mail.not_fiscalized')}}
    </x-mail.footer-below>
</x-mail.layouts.main>
