<x-mail.layouts.main>
    <x-mail.logo></x-mail.logo>
    <x-mail.body>
        <x-mail.row>
            <div style="font-family:Arial, sans-serif;font-size:14px;line-height:28px;text-align:left;color:#55575d;">
                Dear, {{$reservation->leadTraveller?->full_name}}
            </div>
            <div style="font-family:Arial, sans-serif;font-size:14px;line-height:28px;text-align:left;color:#55575d;">
                <b>Your reservation has been modified.</b><br>
                This email is modification of the reservation. Please save this reservation for your travel.
            </div>
        </x-mail.row>

        <x-mail.divider></x-mail.divider>
        <x-mail.row>
            <h3 style="font-weight: bold; margin-top: 0; margin-bottom: 1%"> <span
                    style="color: #3498DB; text-decoration: none">
                                                                          Transfer reservation</span>
            </h3>
            <p style="font-size: 14px">Reservation number : #{{$reservation->id}}</p>
            <p style="font-size: 14px">Name : {{$reservation->leadTraveller?->full_name}}</p>
            <p style="font-size: 14px">Contact Phone : {{$reservation->leadTraveller?->phone}}</p>
            <p style="font-size: 14px">Direction :
                <b>{{ $reservation->returnReservation ? 'Round Trip' : 'One Way' }}</b></p>
        </x-mail.row>
        <x-mail.divider></x-mail.divider>

        <x-mail.row>
            <h3 style="font-weight: bold; margin-top: 0; margin-bottom: 1%"> <span
                    style="color: #3498DB; text-decoration: none">
                                                                          Transfer itinerary</span>
            </h3>

            <p style="font-size: 14px"><b>Pickup Address : </b> {{$reservation->pickup_address}}</p>
            <p style="font-size: 14px"><b>Dropoff Address : </b> {{$reservation->dropoff_address}}</p>
            <p style="font-size: 14px"><b>Pickup Date : </b> {{$reservation->date_time->format('d.m.Y H:i')}}</p>
            @if($reservation->flight_number)
                <p style="font-size: 14px"><b>Flight Number:</b> {{$reservation->flight_number}}</p>
            @endif

            @if($reservation->remark)
                <p style="font-size: 14px"><b>Remark:</b> {{$reservation->remark}}</p>
            @endif
            @if($reservation->returnReservation)
                <small>Please note that this trip is <b>ROUND TRIP</b>. Information are below</small>
                <p style="font-size: 14px"><b>Round Trip Pickup: </b> {{$reservation->dropoff_address}}</p>
                <p style="font-size: 14px"><b>Round Trip Dropoff: </b> {{$reservation->pickup_address}}</p>
                <p style="font-size: 14px"><b>Round Trip
                        Date: </b> {{$reservation->returnReservation->date_time->format('d.m.Y H:i')}}</p>
            @endif
            @if($reservation->adults)
                <p style="font-size: 14px">Adults: {{$reservation->adults}}</p>
            @endif
            @if($reservation->children)
                <p style="font-size: 14px">Children: {{$reservation->children}}</p>
            @endif
            @if($reservation->infants)
                <p style="font-size: 14px">Infants: {{$reservation->infants}}</p>
            @endif

            <p style="font-size: 14px">Luggage: {{$reservation->luggage}}</p>

        </x-mail.row>
        <x-mail.divider></x-mail.divider>

        <x-mail.row>
            <h3 style="font-weight: bold; margin-top: 0; margin-bottom: 1%"> <span
                    style="color: #3498DB; text-decoration: none;">
                    Transfer price breakdown</span>
            </h3>

            @foreach($reservation->price_breakdown as $pbItem)
                <p style="font-size: 14px; margin-bottom: 10px;">{{$loop->index+1}}
                    . {{Arr::get($pbItem,'name')}}
                    : {{Arr::get($pbItem,'amount.formatted')}}</p>
            @endforeach

            <p style="font-size: 18px"><b>Total Price : </b>
                <b>{{\Cknow\Money\Money::EUR($reservation->price)}}</b></p>
        </x-mail.row>


        <x-mail.divider></x-mail.divider>

        <x-mail.row>
            <div
                style="font-family:Roboto, Helvetica, sans-serif;font-size:14px;font-weight:400;line-height:24px;text-align:left;">
                <b>Important notes on your transfer reservation, modifications and
                    cancellations:</b>

                <br>

                <p>Please have your reservation ready to show to your driver
                    (via mobile phone or printed version) in order to reconfirm you
                    have arrived to the right transfer as scheduled and
                    reserved.</p>

            </div>
            <ul>
                <li style="padding-bottom: 20px"><strong>Pick-up time and
                        meeting point: </strong> <br>
                    Airport pick up time and meeting point on arrival: the
                    driver will be waiting outside
                    the baggage claim area at the gate assigned to the specified
                    flight number, holding a
                    pickup sign with passenger name. Time frame refers to the
                    waiting time when flight is
                    on schedule. In case of delays, waiting time is prolonged
                    accordingly. In case you cannot
                    identify your transfer at the designated location, please
                    contact !!!!rezervacijski centar e-mail i broj telefona!!!
                </li>

                <li style="padding-bottom: 20px"><strong>Hotel pick up time and
                        meeting point on departure:</strong><br>
                    - Hotel pick up time and meeting point on departure: timing
                    of return transfer may be adjusted according to road
                    conditions.
                    Please reconfirm your pick up time and meeting point at the
                    reception the day before the departure. <br>
                    - Kindly plan to arrive at least 10 minutes before your
                    transfer departure time.
                </li>

                <li style="padding-bottom: 20px"><strong>In case you need to
                        modify or cancel your booking before travel: </strong>
                    <br>
                    Please contact rezervacijski centar e-mail i broj telefona.
                </li>

                <li style="padding-bottom: 20px"><strong>In case of
                        cancellation, a standard cancellation policy applies as
                        follows:</strong> <br>
                    Raspisat ćemo kako imaju partneri, u smislu cancellations
                    are free of charge up to 24 hours before the .
                </li>

                <li style="padding-bottom: 20px"><strong>Please respect the
                        following rules in the vehicle: </strong> <br>
                    - Smoking is not allowed <br>
                    - Seat belts must be worn at all times.
                </li>
            </ul>
        </x-mail.row>


    </x-mail.body>

    <x-mail.footer>
        Valamar Transfer Service
    </x-mail.footer>

    <x-mail.footer-below>
        This is not fiscalized invoice.
    </x-mail.footer-below>
</x-mail.layouts.main>
