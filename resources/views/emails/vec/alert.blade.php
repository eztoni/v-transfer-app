<div style="padding: 25px 0">
    <p>Poštovani,</p>
        <br>
    <p>
        U nastavku slijedi kategorizirani opis rezervacija nad kojima je potrebna akcija agenta:
        </p>
        @if(!empty($alert_list['missing_reservation_number']))
            Rezervacijama iz liste niže nedostaje <b>Reservation Number</b>,ne mogu biti automatski mapirane,te time nikada ni proslijeđena u Operu.<br/>

            Slijedeće Rezervacije smještaja nemaju pravilan PH broj, potrebno provjeriti transfere niže:

            <ul>
            @foreach($alert_list['missing_reservation_number'] as $booking)

                    @php
                        $destination = Destination::findOrFail($booking->destination_id);

                        $owner = Owner::findOrFail($destination->owner_id);

                        $extra_info = $owner->name.' - '.$destination->name;

                    @endphp

                    <li>{{$extra_info}} - #{{$booking->id}} - {{$booking->leadTraveller->full_name}}</li>
            @endforeach
            </ul>

            <br/>
        @endif

        @if(!empty($alert_list['not_synced']))
            Rezervacije iz liste niže imaju reservation number, ali im nedostaju <b>Opera ID i Opera Confirmation.</b><br/>

            Ove rezervacije smještaja se nisu spustile u Operu. Potrebno provjeriti transfere niže:

            <ul>
                @foreach($alert_list['not_synced'] as $booking)

                    @php
                        $destination = Destination::findOrFail($booking->destination_id);

                        $owner = Owner::findOrFail($destination->owner_id);

                        $extra_info = $owner->name.' - '.$destination->name;

                    @endphp


                    <li>{{$extra_info}} - {{$booking->id.' ('.$booking->getAccommodationReservationCode().')'}} - {{$booking->leadTraveller->full_name}}</li>
                @endforeach
            </ul>
            <br/>
        @endif

        @if(!empty($alert_list['has_data_failed_sync']))

            Rezervacije iz liste niže imaju <b>imaju sve podatke</b> no iz nedefiniranog razloga, nisu mogle biti spuštene u Operu.

           Ove rezervacije smještaja se nisu spustile u Operu. Potrebno provjeriti transfere niže:


        <ul>
                @foreach($alert_list['has_data_failed_sync'] as $booking)

                @php
                    $destination = Destination::findOrFail($booking->destination_id);

                    $owner = Owner::findOrFail($destination->owner_id);

                    $extra_info = $owner->name.' - '.$destination->name;

                @endphp


                <li>{{$extra_info}} - {{$booking->id.' ('.$booking->getAccommodationReservationCode().')'}} - {{$booking->leadTraveller->full_name}}</li>
                @endforeach
            </ul>
            <br/>
        @endif

        <p>Valamar Transfer App</p>
</div>
