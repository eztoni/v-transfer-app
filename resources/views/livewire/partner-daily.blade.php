<div>
    <x-card cardClasses="mb-2" title=" Partner Daily Reservation Report">


        <div class="grid grid-cols-9 gap-1">

            <x-flatpickr
                label="Date from:"
                min-date=""
                date-format="d.m.Y"
                :enable-time="false"
                :default-date="$this->dateFrom"
                wire:model.defer="dateFrom"
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
                wire:model="partner"
                label="Partner"
                :options="$this->partners"
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
