<div>
    <x-card cardClasses="mb-2" title=" Destination Reports">




            <div class="grid grid-cols-4 gap-2">
                @role(\App\Models\User::ROLE_ADMIN.'|'.\App\Models\User::ROLE_SUPER_ADMIN)



                <x-select
                    option-key-value
                    wire:model="destination"
                    label="Destination"
                    :options="$this->adminDestinations->pluck('name','id')"
                ></x-select>
                @endrole

                <x-datetime-picker
                    without-time
                    without-timezone
                    label="Date from"
                    wire:model.defer.defer="dateFrom"
                    display-format="DD.MM.YYYY"
                />
                <x-datetime-picker
                    without-time
                    label="Date to"
                    wire:model.defer.defer="dateTo"
                    display-format="DD.MM.YYYY"
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
                    option-key-value
                    wire:model="status"
                    label="Status"
                    :options="App\Models\Reservation::STATUS_ARRAY"
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
            <div class="ds-stat-desc font-bold">{{$this->totalHRK}}</div>
        </div>

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
                    <th>Date</th>
                    <th>Partner</th>
                    <th>People</th>
                    <th>Transfer</th>
                    <th>Vehicle</th>
                    <th>Status</th>
                    <th class="text-right">Price</th>
                </tr>
                </thead>
                <tbody>

                @foreach($this->filteredReservations as $reservation)
                    <tr>
                        <th>{{Arr::get($reservation,'id')}}</th>
                        <td>{{Arr::get($reservation,'name')}}</td>
                        <td>{{Arr::get($reservation,'date')}}</td>
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
                        <td>{{Arr::get($reservation,'status')}}</td>
                        <td class="text-right">
                            <span class="font-bold"> {{Arr::get($reservation,'price_eur')}}</span>
                            <br>
                            <span class="opacity-75">{{Arr::get($reservation,'price_hrk')}}</span>

                        </td>

                    </tr>
                @endforeach


                </tbody>
            </table>


    </x-card>
    @else




    @endif

</div>
