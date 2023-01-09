<div class=" my-4">
    <p class="text-lg"> Sync reservation #{{$this->reservation->id}} with Opera?</p>
    <div class="ds-divider"></div>
    <div class="flex justify-end">
        <x-button label="Yes"  wire:click="syncReservation"/>
        <x-button label="No"  negative wire:click="close"/>
    </div>
</div>
