<div>
    <x-card cardClasses="mb-2" title="Agent Efficiency Report">


        <div class="grid grid-cols-6 gap-2">

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

            <x-select class="col-span-2"
                option-key-value
                wire:model="agent"
                label="Agent"
                :options="$this->getAgentProperty()"
            ></x-select>

            <x-select
                option-key-value
                wire:model="report_type"
                label="Report by"
                :options="$this->report_types"
            ></x-select>

            <div class="ds-form-control flex-col sm:justify-end">
                <x-button primary wire:click="generate">Generate report</x-button>

            </div>

        </div>


    </x-card>
    <div class="ds-divider"></div>

    @if($this->filteredReservations)

        <div class="ds-stats rounded-lg shadow-md mb-2 border w-full">

            <div class="ds-stat">

                <div class="ds-stat-title">Total Reservations</div>
                <div class="ds-stat-value text-primary">{{count($filteredReservations)}}</div>

            </div>

            <div class="ds-stat ">

                <div class="ds-stat-title">Total revenue</div>
                <div class="ds-stat-value text-success">{{$this->totalEur}} €</div>
            </div>

        </div>
        <div class="ds-divider"></div>

        <x-card>
            <div class="flex justify-end mb-2">
                Export to excel:



                <button class="ds-btn ds-btn-outline ds-btn-sm ds-btn-square ml-2 "
                        wire:loading.class="ds-loading"
                        wire:target="exportToExcel"
                        wire:click="exportToExcel">

                    <x-icon name="document-download" wire:loading.remove wire:target="exportToExcel" class="w-5 h-5 ">
                    </x-icon>
                </button>

            </div>

            <table class="ds-table max-w-full ds-table-compact">
                <tr  style="font-size: 10px !important">
                    @if($this->isAgentReporting)

                        <th>ID</th>
                        <th>Partner</th>
                        <th>Ruta</th>
                        <th>Dvosmijerno?</th>
                        <th>Datum Rezervacije</th>
                        <th>Datum i Vrijeme Transfera</th>
                        <th>Transfer</th>
                        <th>Gost</th>
                        <th>Iznos</th>

                    @endif
                </tr>
                </thead>
                <tbody  style="font-size: 10px !important">
                @foreach($this->filteredReservations as $reservation)
                    <tr>

                        @if($this->isAgentReporting)

                            <td>{{Arr::get($reservation,'id')}}</td>
                            <td>{{Arr::get($reservation,'partner')}}</td>
                            <td>{{Arr::get($reservation,'route')}}</td>
                            <td>{{Arr::get($reservation,'round_trip') ? 'Da' : 'Ne'}}</td>
                            <td>{{Arr::get($reservation,'created_at')}}</td>
                            <td>{{Arr::get($reservation,'date_time')}}</td>
                            <td>{{Arr::get($reservation,'transfer')}}</td>
                            <td>{{Arr::get($reservation,'name')}}</td>
                            <td>{{Arr::get($reservation,'price')}} €</td>

                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>

        </x-card>
    @else
        <div class="ds-divider"></div>
        <div class="ds-form-control flex-col justify-end">
            <small class="ds-alert-warning ds-form-control flex-col text-center">{{ $message }}</small>
        </div>
    @endif

</div>
