<div style="padding: 25px 0">
    <p>Poštovani,</p>
        <br>
    <p>
        Molimo da se obrati pozornost na rezervaciju #<b>{{ $reservation->id }} - {{ $reservation->leadTraveller->full_name }}. ( {{ $reservation->date_time->format('d.m.Y') }} )</b><br>

        U rezervaciji smještaja promijenjen je broj osoba, potrebno je provjeriti transfer #broj transfera. {{ $reservation->id }}.</b><br/>

        </p>
        <p>Valamar Transfer App</p>
</div>
