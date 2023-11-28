<div class=" my-4">

    <div class="flex">
        <x-flatpickr
            wire:model="cancellationDate"
            label="Cancellation Date and Time:"
            min-date=""
            date-format="Y-m-d H:i:ss"
            :enable-time="true"
        />
    </div>

    <div class="ds-divider"></div>
    <b class="text">{{$partnerName}} - Cancellation Fee Conditions</b><br>
    <p class="text sm">{{$partnerConditions}}</p>
    <div class="ds-divider"></div>
    @if($reservation->included_in_accommodation_reservation == 0 && $reservation->v_level_transfer == 0)
        <p class="text">Reservation Total For Guest: {{$displayPrice}}</p>
    @else
        <p class="text">Reservation Total For Guest: 0,00 €</p>
    @endif

    @if($reservation->included_in_accommodation_reservation == 1 || $reservation->v_level_transfer == 1)
        <small class="text">Internal Cost: {{$displayPrice}}</small>
    @endif
    <div class="ds-divider"></div>

    <x-native-select
        wire:model="cancellationType"
        label="Reservation Status:"
        option-key-value
        :options="$cancellationTypeOptions"
    />

    <x-input type="number" min="1" max="100" label="Cancellation Fee %" wire:model="cancellation_fee_percent"></x-input>
    <x-input  type="number" label="Cancellation Fee €" wire:model="cancellation_fee_nominal"></x-input>
    <br/>

    <div class="flex justify-end my-3">
        <x-checkbox lg class="justify-end ml-auto" left-label="Send Cancellation Fee 0 to opera"
                    wire:model="cf_null"/>
    </div>
    <br/>

    @if($reservation->isRoundTrip())
        <div class="flex justify-end my-3">
            <x-checkbox lg class="justify-end ml-auto" left-label="Cancel Round trip"
                        wire:model="cancelRoundTrip"/>
        </div>
        <br/>
    @endif


    <label style="font-size: 80%" primary class="flex justify-end">{{$infoMessage}}</label>
    <br/>

    <div class="flex justify-end">
        <x-button label="Yes" negative wire:click="cancelReservation()"/>

        <x-button label="No"  wire:click="close"/>
    </div>



</div>
