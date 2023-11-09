<div>



    <div class="divider"></div>
    <x-flatpickr
        :class="$reservation->isDirty('date_time')?'border-success':''"
        label="Date & time:"
        :default-date="$this->date"
        wire:model.defer.defer="date"
    ></x-flatpickr>


    <x-input :class="$reservation->isDirty('adults')?'border-success':''"
                          wire:model.defer="adults"
             type="number"
                          label="Adults"
    />
    <x-input
        :class="$reservation->isDirty('children')?'border-success':''"
        wire:model.defer="children"
        type="number"

        label="Children"
    />
    <x-input
        :class="$reservation->isDirty('infants')?'border-success':''"
        wire:model.defer="infants"
        type="number"

        label="Infants"
    />
    <x-input
        :class="$reservation->isDirty('luggage')?'border-success':''"
        wire:model.defer="luggage"
        type="number"

        label="Luggage"
    />
    <x-input
        :class="$reservation->isDirty('flight_number')?'border-success':''"
        wire:model.defer="flight_number"
                          label="Flight number"
    />

    <x-textarea
        label="Remark:"
        wire:model.defer="remark"
    />

    <x-native-select
        label="Send Modify Email:"
        :options="[
            ['name' => 'Yes',  'id' => 1],
            ['name' => 'No', 'id' => 0],
        ]"
        option-label="name"
        option-value="id"
        wire:model.defer="sendModifyMail"
    />



    <div class=" my-4">
        <x-button positive wire:click="confirmationDialog">
            Save
        </x-button>
        <x-button  wire:click="cancel">
            Cancel
        </x-button>
    </div>
</div>
