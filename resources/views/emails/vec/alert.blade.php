<div style="padding: 25px 0">
    <p>Poštovani,</p>
        <br>
    <p>
        U nastavku slijedi kategorizirani opis rezervacija nad kojima je potrebna akcija agenta:
        </p>
        @if(!empty($alert_list['missing_reservation_number']))
            Rezervacijama iz liste niže nedostaje <b>Reservation Number</b>,ne mogu biti automatski mapirane,te time nikada ni proslijeđena u Operu.<br/>
    
            Za ove rezervacije potrebno je u pregledu rezervacija unijeti ispravan reservation number ( PHCode itd ).

            <ul>
            @foreach($alert_list['missing_reservation_number'] as $booking)
                    <li>{{$booking->id}} - {{$booking->leadTraveller->full_name}}</li>
            @endforeach
            </ul>

            <br/>
        @endif

        @if(!empty($alert_list['not_synced']))
            Rezervacije iz liste niže imaju reservation number, ali im nedostaju <b>Opera ID i Opera Confirmation.</b><br/>

            Za ove rezervacije, sustav nije mogao iz MDP-a povući rezervaciju po postavljenom reservation numberu, te je isti potrebno provjeriti.

            <ul>
                @foreach($alert_list['not_synced'] as $booking)
                    <li>{{$booking->id.' ('.$booking->getAccommodationReservationCode().')'}} - {{$booking->leadTraveller->full_name}}</li>
                @endforeach
            </ul>
            <br/>
        @endif

        @if(!empty($alert_list['has_data_failed_sync']))

            Rezervacije iz liste niže imaju <b>imaju sve podatke</b> no iz nedefiniranog razloga, nisu mogle biti spuštene u Operu.

            Za ove rezervacije, potrebno je provjeriti Opera Sync Logove dostupne na pregledu individualne rezervacije.

            <ul>
                @foreach($alert_list['has_data_failed_sync'] as $booking)
                    <li>{{$booking->id.' ('.$booking->getAccommodationReservationCode().')'}} - {{$booking->leadTraveller->full_name}}</li>
                @endforeach
            </ul>
            <br/>
        @endif

        <p>Valamar Transfer App</p>
</div>
