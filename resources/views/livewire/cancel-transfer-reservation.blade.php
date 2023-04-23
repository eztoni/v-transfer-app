<div class=" my-4">
    <p class="text-lg"> Are you sure you want to cancel this reservation?</p>
    <div class="ds-divider"></div>
    <div class="flex">
        <x-flatpickr
            wire:model="cancellationDate"
            label="Cancellation Date and Time:"
            min-date=""
            date-format="Y-m-d H:i:ss"
            :enable-time="true"
            :value="now()->addHour()"
        />
    </div>
<div class="flex justify-end">
    <x-button label="Yes" negative wire:click="cancelReservation()"/>

    <x-button label="No"  wire:click="close"/>
</div>


</div>
