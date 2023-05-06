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
    <b class="text">{{$partnerName}} - Cancellation Fee Conditions</b>
    <div class="ds-divider"></div>

    <x-native-select
        wire:model="cancellationType"
        label="Cancellation Type:"
        option-key-value
        :options="$cancellationTypeOptions"
    />

    <x-input type="number" min="1" max="100" label="Cancellation Fee %" wire:model="cancellation_fee_percent"></x-input>
    <x-input  type="number" label="Cancellation Fee €" wire:model="cancellation_fee_nominal"></x-input>
    <br/>
    <label style="font-size: 80%" primary class="flex justify-end">{{$infoMessage}}</label>
    <br/>
<div class="flex justify-end">
    <x-button label="Yes" negative wire:click="cancelReservation()"/>

    <x-button label="No"  wire:click="close"/>
</div>
</div>
