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
                    @if($this->isPPOMReporting)
                        <th>Kontigent</th>
                        <th>Prodajno Mjesto</th>
                        <th>Vrsta Plaćanja</th>
                        <th>Porezna grupa</th>
                        <th>Vezani Račun Id</th>
                        <th>Datum Prodaje</th>
                        <th>Postupak</th>
                        <th>Bruto Prihod</th>
                        <th>Ugovorena Provizija</th>
                        <th>Trošak Ulaznog Računa</th>
                        <th>Bruto Profit</th>
                        <th>PDV</th>
                        <th>Neto profit</th>
                    @endif

                    @if($this->isRPOReporting)
                        <th>Partner</th>
                        <th>Kontigent</th>
                        <th>Prodajno Mjesto</th>
                        <th>Postupak</th>
                        <th>Datum Prodaje</th>
                        <th>Datum Vouchera</th>
                        <th>Broj Računa</th>
                        <th>Proizvod</th>
                        <th>Porezna grupa</th>
                        <th>Vezani račun Id</th>
                        <th>Količina</th>
                        <th>Bruto prihod</th>
                        <th>Bruto profit</th>
                        <th>Trošak ulaznog računa</th>
                        <th>Ugovorena Provizija</th>
                    @endif

                    @if($this->isPartnerReporting)
                        <th>Datum Vouchera</th>
                        <th>Prodajno Mjesto</th>
                        <th>Voucher ID</th>
                        <th>Nositelj Vouchera</th>
                        <th>Postupak</th>
                        <th>Broj Odraslih</th>
                        <th>Broj Djece</th>
                        <th>Bruto Prihod</th>
                        <th>Trošak Ulaznog računa</th>
                        <th>Bruto profit</th>
                        <th>Ugovorena Provizija</th>
                        <th>Vrsta proizvoda</th>
                    @endif

                    @if($this->isAgentReporting)
                        <th>Prodajni Agent</th>
                        <th>Kontigent</th>
                        <th>Količina</th>
                        <th>Bruto Prihod</th>
                        <th>Trošak Ulaznog Računa</th>
                        <th>Ugovorena provizija</th>
                        <th>Bruto profit</th>
                        <th>PDV</th>
                        <th>Neto Profit</th>
                        @endif
                </tr>
                </thead>
                <tbody  style="font-size: 10px !important">
                @foreach($this->filteredReservations as $reservation)
                    <tr>
                        @if($this->isPPOMReporting)
                            <td>{{Arr::get($reservation,'transfer')}}</td>
                            <td>VEC Valamar</td>
                            <td>REZERVACIJA NA SOBU</td>
                            <td>{{Arr::get($reservation,'tax_level')}}</td>
                            <td>{{Arr::get($reservation,'invoice_number')}}</td>
                            <td>{{Arr::get($reservation,'voucher_date')}}</td>
                            <td>{{Arr::get($reservation,'status') == 'confirmed' ? 'RP' : 'CF'}}</td>
                            <td>{{Arr::get($reservation,'price_eur')}}</td>
                            <td align="center">{{Arr::get($reservation,'commission')}} %</td>
                            <td align="center">{{Arr::get($reservation,'invoice_charge')}}</td>
                            <td align="center">{{Arr::get($reservation,'commission_amount')}}</td>
                            <td align="center">{{Arr::get($reservation,'pdv')}}</td>
                            <td align="center">{{Arr::get($reservation,'net_income')}}</td>
                        @endif

                        @if($this->isRPOReporting)
                                <td>{{Arr::get($reservation,'partner')}}</td>
                                <td>{{Arr::get($reservation,'transfer')}}</td>
                                <td>VEC Valamar</td>
                                <td>{{Arr::get($reservation,'status') == 'confirmed' ? 'RP' : 'CF'}}</td>
                                <td>{{Arr::get($reservation,'voucher_date')}}</td>
                                <td>{{Arr::get($reservation,'voucher_date')}}</td>
                                <td>{{Arr::get($reservation,'invoice_number')}}</td>
                                <td>{{Arr::get($reservation,'transfer')}}</td>
                                <td>{{Arr::get($reservation,'tax_level')}}</td>
                                <td>-</td>
                                <td>1</td>
                                <td>{{Arr::get($reservation,'price_eur')}}</td>
                                <td align="center">{{Arr::get($reservation,'commission_amount')}}</td>
                                <td align="center">{{Arr::get($reservation,'invoice_charge')}}</td>
                                <td align="center">{{Arr::get($reservation,'commission')}} %</td>

                        @endif

                    </tr>
                @endforeach
                </tbody>
            </table>

        </x-card>
    @else




    @endif

</div>
