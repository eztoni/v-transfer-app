<div>
    <x-card cardClasses="mb-2" title=" Destination Reports">


        <div class="grid grid-cols-4 gap-2">
            @role(\App\Models\User::ROLE_ADMIN.'|'.\App\Models\User::ROLE_SUPER_ADMIN)

            <x-native-select
                option-key-value
                wire:model="destination"
                label="Destination"
                clearable="false"
                :options="$this->adminDestinations"
            ></x-native-select>

            @endrole

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

            <x-select
                option-key-value
                wire:model="pickupLocation"
                label="Pickup location"
                :options="$this->pickupLocations"
            ></x-select>

            <x-select
                option-key-value
                wire:model="dropoffLocation"
                label="Dropoff location"
                :options="$this->dropoffLocations"
            ></x-select>

            <x-select
                wire:model="status"
                label="Status"
                :options="['All']+App\Models\Reservation::STATUS_ARRAY"
            ></x-select>

            <div class="ds-form-control flex-col justify-end">
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
                <div class="ds-stat-value text-success">{{$this->totalEur}}</div>
            </div>

            @if($this->isPartnerReporting)
            <div class="ds-stat ">

                <div class="ds-stat-title">Total commission</div>
                <div class="ds-stat-value text-warning-400">{{$this->totalCommission}}</div>
            </div>
@endif
            <div class="ds-stat">
                <div class="ds-stat-title">Confirmed reservations:</div>

                <div class="ds-stat-value text-primary">

                    @php
                        echo count( Arr::where($filteredReservations, function ($value, $key) {
                               return $value['status'] === \App\Models\Reservation::STATUS_CONFIRMED;
                           }))
                    @endphp
                </div>

                <div class="ds-stat-desc font-bold">
                    Cancelled reservations:
                    @php
                        echo count( Arr::where($filteredReservations, function ($value, $key) {
                               return $value['status'] === \App\Models\Reservation::STATUS_CANCELLED;
                           }))
                    @endphp
                </div>

            </div>

        </div>
        <div class="ds-divider"></div>

        <x-card>
            <table class="ds-table ds-table-compact w-full">
                <thead>
                <tr>
                    <th></th>
                    <th>Guest</th>
                    <th>R.t.</th>
                    <th>Date</th>
                    <th>Partner</th>
                    <th>People</th>
                    <th>Transfer</th>
                    <th>Vehicle</th>
                    @if($this->isPartnerReporting)
                        <th>Tax lvl</th>
                        <th>Comm. %</th>
                        <th>Comm.</th>
                    @endif
                    <th class="text-center">Status</th>
                    <th class="text-right">Price</th>
                </tr>
                </thead>
                <tbody>

                @foreach($this->filteredReservations as $reservation)
                    <tr>
                        <th>{{Arr::get($reservation,'id')}}</th>
                        <td>{{Arr::get($reservation,'name')}}</td>
                        <td>
                            @if(Arr::get($reservation,'round_trip'))
                                <x-icon name="check-circle" class="w-5 h-5 text-success"/>
                            @else
                                <x-icon name="x-circle" class="w-5 h-5 text-negative-500"/>

                            @endif
                        </td>
                        <td>
                            <p class="flex gap-2">
                                <x-icon name="arrow-right" class="w-5 h-5"/>
                                <label>{{Arr::get($reservation,'date_time')}}</label>
                            </p>
                            @if(Arr::get($reservation,'round_trip'))

                                <p class="flex gap-2">
                                    <x-icon name="arrow-left" class="w-5 h-5"/>
                                    <label>{{Arr::get($reservation,'round_trip_date')}}</label>
                                </p>
                            @endif

                        </td>
                        <td>{{Arr::get($reservation,'partner')}}</td>
                        <td>
                            <span>A: {{Arr::get($reservation,'adults')}}</span>

                            @if(Arr::get($reservation,'children'))
                                <br>
                                <span>C: {{Arr::get($reservation,'children')}}</span>

                            @endif
                            @if(Arr::get($reservation,'infants'))
                                <br>
                                <span>I: {{Arr::get($reservation,'infants')}}</span>
                            @endif

                        </td>
                        <td>{{Arr::get($reservation,'transfer')}}</td>
                        <td>{{Arr::get($reservation,'vehicle')}}</td>
                        @if($this->isPartnerReporting)
                            <td>{{Arr::get($reservation,'tax_level')}}</td>
                            <td>{{Arr::get($reservation,'commission')}} %</td>
                            <td>{{Arr::get($reservation,'commission_amount')}}</td>

                        @endif
                        <td class="text-center ">

                            @switch(Arr::get($reservation,'status'))
                                @case(\App\Models\Reservation::STATUS_PENDING)
                                    <x-icon name="question-mark-circle" class="text-warning-600 w-5 h-5 mx-auto"/>
                                    @break
                                @case(\App\Models\Reservation::STATUS_CONFIRMED)
                                    <x-icon name="check-circle" class="text-success w-5 h-5 mx-auto"/>
                                    @break
                                @case(\App\Models\Reservation::STATUS_CANCELLED)
                                    <x-icon name="x-circle" class="text-negative-500 w-5 h-5 mx-auto"/>
                                    @break
                            @endswitch
                        </td>
                        <td class="text-right">
                            <span class="font-bold"> {{Arr::get($reservation,'price_eur')}}</span>

                        </td>

                    </tr>
                @endforeach


                </tbody>
            </table>


        </x-card>
    @else




    @endif

</div>
