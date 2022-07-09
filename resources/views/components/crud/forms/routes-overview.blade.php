<div >


    <x-input wire:model="model.name" label="Name" placeholder="ex. Hotel to airport"></x-input>
    <x-native-select label="Starting point" wire:model="model.starting_point_id" option-key-value :options="$this->startingPoints"></x-native-select>
    <x-native-select label="Ending point" wire:model="model.ending_point_id" option-key-value :options="$this->endingPoints"></x-native-select>
    <x-input wire:model="model.pms_code" label="PMS code" ></x-input>


</div>
