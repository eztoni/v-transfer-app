<div>
    <x-card cardClasses="mb-2" title=" Reception Daily Reservation Report">


        <div class="grid grid-cols-9 gap-1">

            <x-flatpickr
                label="Date from:"
                min-date=""
                date-format="d.m.Y"
                :enable-time="false"
                :default-date="$this->dateFrom"
                wire:model="dateFrom"
            />

            <x-flatpickr
                label="Date to:"
                min-date=""
                date-format="d.m.Y"
                :enable-time="false"
                :default-date="$this->dateTo"
                wire:model.defer="dateTo"
            />

            <x-select
                option-key-value
                wire:model="accommodation"
                label="Select Hotel"
                :options="$this->getAccommodationProperty()"
                x-init="3"

            ></x-select>
            <br/>
            <div class="ds-form-control flex-col justify-end">
                <x-button primary wire:click="generate">{{ $button_message }}</x-button>
            </div>
            <br/>
            <div class="ds-form-control flex-col justify-end">
                <small class="ds-alert-warning ds-form-control flex-col text-center">{{ $message }}</small>
            </div>

        </div>

    </x-card>

</div>
