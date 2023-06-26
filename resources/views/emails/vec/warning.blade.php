<div style="padding: 25px 0">
    <p>Poštovani,</p>
        <br>
    <p>
        Molimo da se obrati pozornost na rezervaciju #<b>{{ $reservation->id }} - {{ $reservation->leadTraveller->full_name }}. ( {{ $reservation->date_time->format('d.m.Y') }} )</b><br>

        Modifikacija rezervacije sadrži više osoba, nego što je podržano za transfer partnera <b>{{$reservation->partner->name}} - {{ $reservation->Transfer->name }}.</b><br/>

        Za vozilo kojim se obavlja transfer {{$reservation->partner->name}} - {{ $reservation->Transfer->name }},<b style="color:green"> maksimalni kapacitet je {{ $reservation->Transfer->Vehicle->max_occ }}</b>, dok je modifikacijom rezervacije broj osoba za koje je potreban transfer: <b style="color:red">{{ $reservation->getNumPassangersAttribute() }}</b>.
        </p>
        <p>Valamar Transfer App</p>
</div>
