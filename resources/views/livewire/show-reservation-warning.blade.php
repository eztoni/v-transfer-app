<div class=" my-4">
    @if($this->reservation)
        <p>Transfer has already been downloaded for this accommodation booking with the following details:</p><br/>
        <hr>
        <br/>
        <b>ReservationID: </b>{{$this->reservation->id}}<br/>
        <b>Reservation Number: </b> {{$this->reservation->leadTraveller->reservation_number}}<br/>
        <b>Reservation OperaID: </b> {{$this->reservation->leadTraveller->reservation_opera_id}}<br/>
        <b>Reservation Opera Confirmation: </b> {{$this->reservation->leadTraveller->reservation_opera_confirmation}}<br/>
        <b>Guest: </b>{{$this->reservation->leadTraveller->first_name}} {{$this->reservation->leadTraveller->last_name}}<br/>
        <b>Route: </b>{{$reservation->pickupAddress->name}} => {{$reservation->dropoffAddress->name}}<br/>
        <b>Date\Time: </b>{{\Carbon\Carbon::parse($reservation->date_time->format('d.m.Y H:i'))->format('d.m.Y H:i')}}<br/>
        @if($this->reservation->is_round_trip)
            <b>TransferType:</b> RoundTrip
        @else
            <b>TransferType:</b> OneWay
        @endif
        <br/>
        <b>Created by: </b>{{$reservation->createdBy->name}}<br/>

    @endif
</div>
