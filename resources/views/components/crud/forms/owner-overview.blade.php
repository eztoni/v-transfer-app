<div >


    <x-input wire:model="model.name" label="Name" placeholder="Valamar rivijera"></x-input>

    @if(!$this->isUpdate)
    <hr class="my-4">

    <x-input wire:model="destination_name" label="Destination name"
    hint="When creating Owner, one destination needs to be created."></x-input>
    @endif

</div>
