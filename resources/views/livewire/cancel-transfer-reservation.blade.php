<div class=" my-4">
    <p class="text-lg"> Are you sure you want to cancel this reservation?</p>
    <div class="ds-divider"></div>


<div class="flex justify-end">
    <x-button label="Yes" negative wire:click="cancelReservation"/>

    <x-button label="No"  wire:click="close"/>
</div>


</div>
