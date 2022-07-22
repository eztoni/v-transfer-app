<div>


    <x-dialog z-index="z-50" blur="md" align="end" />

    <div class="divider"></div>
    <x-flatpickr
        :class="$reservation->isDirty('date_time')?'border-success':''"
        label="Date & time:"
        :default-date="$this->date"
        wire:model.defer="date"
    ></x-flatpickr>


    <x-input :class="$reservation->isDirty('adults')?'border-success':''"
                          wire:model="reservation.adults"
                          label="Adults"
    />
    <x-input
        :class="$reservation->isDirty('children')?'border-success':''"
        wire:model="reservation.children"
                          label="Children"
    />
    <x-input
        :class="$reservation->isDirty('infants')?'border-success':''"
        wire:model="reservation.infants"
                          label="Infants"
    />
    <x-input
        :class="$reservation->isDirty('luggage')?'border-success':''"
        wire:model="reservation.luggage"
                          label="Luggage"
    />
    <x-input
        :class="$reservation->isDirty('flight_number')?'border-success':''"
        wire:model="reservation.flight_number"
                          label="Flight number"
    />

    <x-textarea
        label="Remark:"
        wire:model="reservation.remark"
    />

    <x-select label="Send Modify Email:"
              option-key-value
              :options="$this->sendEmailArray"
              wire:model="sendModifyMail"
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
