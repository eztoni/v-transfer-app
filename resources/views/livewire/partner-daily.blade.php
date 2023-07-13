<div>
    <x-card cardClasses="mb-2" title=" Reception Daily Reservation Report">


        <div class="grid grid-cols-9 gap-1">

            <x-flatpickr
                label="Reservation Report Date:"
                min-date=""
                date-format="d.m.Y"
                :enable-time="false"
                :default-date="$this->dateFrom"
                wire:model="dateFrom"
            />
            <x-select
                option-key-value
                wire:model="accommodation"
                label="Select Hotel"
                :options="$this->getAccommodationProperty()"
                x-init="3"

            ></x-select>
            <div class="ds-divider"></div>
            <div class="ds-form-control flex-col justify-end">
                <x-button primary wire:click="generate">{{ $button_message }}</x-button>
            </div>
            <div class="ds-form-control flex-col justify-end">
                <small class="ds-alert-warning ds-form-control flex-col text-center">{{ $message }}</small>
            </div>

        </div>

    </x-card>

</div>
