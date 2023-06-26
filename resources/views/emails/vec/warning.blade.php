<div style="padding: 25px 0">
    <p>Poštovani,</p>
        <br>
    <p>
        Molimo da se obrati pozornost na rezervaciju #{{ $reservation->id }} - {{ $reservation->leadTraveller->full_name }} ().<br>

        Modifikacija rezervacije sadrži više osoba, nego što je podržano za transfer partnera {{$reservation->partner->name}} - {{ $reservation->Transfer->name }}.

        Za vozilo kojim se obavlja transfer {{$reservation->partner->name}} - {{ $reservation->Transfer->name }}, maksimalni kapacitet je {{ $reservation->Transfer->Vehicle->max_occ }}, dok je modifikacijom rezervacije broj osoba za koje je potreban transfer: {{ $reservation->getNumPassangersAttribute() }}.
        </p>

    <br>

        <p>Valamar Transfer App</p>
</div>
