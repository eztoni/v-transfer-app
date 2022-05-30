<div>
    <x-ez-card class="mb-2">
        <x-slot name="title">
            Destination Reports
        </x-slot>

        <x-slot name="body">
            <div class="grid grid-cols-4 gap-2">
                @role(\App\Models\User::ROLE_ADMIN.'|'.\App\Models\User::ROLE_SUPER_ADMIN)
                <x-form.ez-select
                    :show-empty-value="false"
                    model="destination"
                    label="Destination"
                    :items="$this->adminDestinations"
                    sm="true"
                ></x-form.ez-select>
                @endrole
                <div class="form-control">
                    <label class="label ">
                        <span class="label-text ">Date from:</span>
                    </label>
                    <input x-init="
                                        flatpickr($el, {
                                        disableMobile: 'true',
                                        minDate:'{{\Carbon\Carbon::now()->subYears(3)->format('Y-m-d')}}',
                                        dateFormat:'d.m.Y',
                                        defaultDate:'{{$dateFrom}}'});
                                        " readonly
                           wire:model="dateFrom"
                           class=" input input-bordered input-sm "
                           placeholder="Date to:">

                    @error('dateFrom')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>
                <div class="form-control">
                    <label class="label ">
                        <span class="label-text ">Date to:</span>
                    </label>
                    <input x-init="
                                        flatpickr($el, {
                                        disableMobile: 'true',
                                        minDate:'{{\Carbon\Carbon::now()->subYears(3)->format('Y-m-d')}}',
                                        dateFormat:'d.m.Y',
                                        defaultDate:'{{$dateTo}}'});
                                        " readonly
                           wire:model="dateTo"
                           class=" input input-bordered input-sm "
                           placeholder="Date to:">

                    @error('dateTo')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>
                <x-form.ez-select
                    :show-empty-value="false"
                    model="partner"
                    label="Partner"
                    :items="$this->partners"
                    sm="true"
                ></x-form.ez-select>
                <x-form.ez-select
                    :show-empty-value="false"
                    model="pickupLocation"
                    label="Pickup location"
                    :items="$this->pickupLocations"
                    sm="true"
                ></x-form.ez-select>
                <x-form.ez-select
                    :show-empty-value="false"
                    model="dropoffLocation"
                    label="Dropoff location"
                    :items="$this->dropoffLocations"
                    sm="true"
                ></x-form.ez-select>
                <x-form.ez-select
                    :show-empty-value="false"
                    model="status"
                    label="Status"
                    :items="$this->statuses"
                    sm="true"
                ></x-form.ez-select>

                <div class="form-control">
                    <label class="label ">
                        <span class="label-text ">&nbsp;</span>
                    </label>
                    <button class="btn btn-success btn-sm" wire:click="generate">Generate report</button>
                </div>

            </div>
        </x-slot>


    </x-ez-card>
    <div class="divider"></div>
    <div class="stats shadow-lg mb-2 border w-full">

        <div class="stat">

            <div class="stat-title">Total Reservations</div>
            <div class="stat-value text-primary">{{count($filteredReservations)}}</div>

        </div>

        <div class="stat">

            <div class="stat-title">Total revenue</div>
            <div class="stat-value text-success">{{$this->totalEur}}</div>
            <div class="stat-desc font-bold">{{$this->totalHRK}}</div>
        </div>

        <div class="stat">
            <div class="stat-title">Confirmed reservations:</div>

            <div class="stat-value text-primary">

              @php
                 echo count( Arr::where($filteredReservations, function ($value, $key) {
                        return $value['status'] === \App\Models\Reservation::STATUS_CONFIRMED;
                    }))
              @endphp
            </div>

            <div class="stat-desc font-bold">
                Cancelled reservations:
                @php
                    echo count( Arr::where($filteredReservations, function ($value, $key) {
                           return $value['status'] === \App\Models\Reservation::STATUS_CANCELLED;
                       }))
                @endphp
            </div>

        </div>

    </div>
    <x-ez-card>
        <x-slot name="body">
            <table class="table table-compact w-full">
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
                                <span>C.: {{Arr::get($reservation,'children')}}</span>

                            @endif
                            @if(Arr::get($reservation,'infants'))
                                <br>
                                <span>I.: {{Arr::get($reservation,'infants')}}</span>
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
        </x-slot>


    </x-ez-card>
</div>
