<div >


    <x-input wire:model="model.name" label="Name" placeholder="ex. Hotel to airport"></x-input>
    <x-select label="Starting point" wire:model="model.starting_point_id" searchable option-key-value :options="$this->startingPoints"></x-select>
    <x-select label="Ending point" wire:model="model.ending_point_id" searchable option-key-value :options="$this->endingPoints"></x-select>
    <x-input wire:model="model.pms_code" label="PMS code" ></x-input>


</div>
