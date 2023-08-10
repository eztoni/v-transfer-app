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

            <table width="100% !important" class="ds-table-zebra ds-table-compact w-full">
                <tr  style="font-size: 10px !important">
                    @if($this->isPPOMReporting)
                        <th align="left">Partner</th>
                        <th align="left">Kontigent</th>
                        <th align="left">Naziv Djelatnika</th>
                        <th align="left">Prodajno Mjesto</th>
                        <th align="left">Vrsta Plaćanja</th>
                        <th align="left">Porezna grupa</th>
                        <th align="left">Broj potvrde narudžbe transfera</th>
                        <th align="left">Datum Prodaje</th>
                        <th align="left">Datum Realizacije</th>
                        <th align="left">Postupak</th>
                        <th align="left">Bruto Prihod</th>
                        <th align="left">Ugovorena Provizija</th>
                        <th align="left">Trošak Ulaznog Računa</th>
                        <th align="left">Bruto Profit</th>
                        <th align="left">PDV</th>
                        <th align="left">Neto profit</th>
                    @endif

                    @if($this->isRPOReporting)
                        <th align="left">Partner</th>
                        <th align="left">Kontigent</th>
                        <th align="left">Naziv Djelatnika</th>
                        <th align="left">Prodajno Mjesto</th>
                        <th align="left">Postupak</th>
                        <th align="left">Datum Prodaje</th>
                        <th align="left">Datum Realizacije</th>
                        <th align="left">Broj Računa</th>
                        <th align="left">Proizvod</th>
                        <th align="left">Porezna grupa</th>
                        <th align="left">Broj potvrde rezervacije</th>
                        <th align="left">Količina</th>
                        <th align="left">Bruto prihod</th>
                        <th align="left">Bruto profit</th>
                        <th align="left">Trošak ulaznog računa</th>
                        <th align="left">Ugovorena Provizija</th>
                    @endif

                    @if($this->isPartnerReporting)
                        <th align="left">Partner</th>
                        <th align="left">Datum Vouchera</th>
                        <th align="left">Naziv Djelatnika</th>
                        <th align="left">Prodajno Mjesto</th>
                        <th align="left">Voucher ID</th>
                        <th align="left">Nositelj Vouchera</th>
                        <th align="left">Postupak</th>
                        <th align="left">Broj Odraslih</th>
                        <th align="left">Broj Djece</th>
                        <th align="left">Bruto Prihod</th>
                        <th align="left">Trošak Ulaznog računa</th>
                        <th align="left">Bruto profit</th>
                        <th align="left">Ugovorena Provizija</th>
                        <th align="left">Vrsta proizvoda</th>
                    @endif

                    @if($this->isAgentReporting)
                        <th>Naziv Djelatnika</th>
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
                <tbody>
                @foreach($this->filteredReservations as $reservation)
                    <tr>
                        @if($this->isPPOMReporting)
                            <td >{{Arr::get($reservation,'partner')}}</td>
                            <td >{{Arr::get($reservation,'transfer')}}</td>
                            <td >{{Arr::get($reservation,'sales_agent')}}</td>
                            <td >{{Arr::get($reservation,'selling_place')}}</td>
                            <td >REZERVACIJA NA SOBU</td>
                            <td >{{Arr::get($reservation,'tax_level')}}</td>
                            <td >{{gmdate('Y').'-'.Arr::get($reservation,'invoice_number')}}</td>
                            <td >{{Arr::get($reservation,'voucher_date')}}</td>
                            <td >{{Arr::get($reservation,'date_time')}}</td>
                            <td >{{Arr::get($reservation,'procedure')}}</td>
                            <td >{{Arr::get($reservation,'price_eur')}}</td>
                            <td align="center" >{{Arr::get($reservation,'commission')}} %</td>
                            <td align="center" >{{Arr::get($reservation,'invoice_charge')}}</td>
                            <td align="center" >{{Arr::get($reservation,'commission_amount')}}</td>
                            <td align="center" >{{Arr::get($reservation,'pdv')}}</td>
                            <td align="center" >{{Arr::get($reservation,'net_income')}}</td>
                        @endif

                        @if($this->isRPOReporting)
                                <td >{{Arr::get($reservation,'partner')}}</td>
                                <td >{{Arr::get($reservation,'transfer')}}</td>
                                <td >{{Arr::get($reservation,'sales_agent')}}</td>
                                <td >{{Arr::get($reservation,'selling_place')}}</td>
                                <td >{{Arr::get($reservation,'procedure')}}</td>
                                <td >{{Arr::get($reservation,'voucher_date')}}</td>
                                <td >{{Arr::get($reservation,'date_time')}}</td>
                                <td >{{gmdate('Y').'-'.Arr::get($reservation,'invoice_number')}}</td>
                                <td >{{Arr::get($reservation,'transfer')}}</td>
                                <td >{{Arr::get($reservation,'tax_level')}}</td>
                                <td >-</td>
                                <td >1</td>
                                <td >{{Arr::get($reservation,'price_eur')}}</td>
                                <td align="center" >{{Arr::get($reservation,'commission_amount')}}</td>
                                <td align="center" >{{Arr::get($reservation,'invoice_charge')}}</td>
                                <td align="center" >{{Arr::get($reservation,'commission')}} %</td>

                        @endif

                        @if($this->isPartnerReporting)
                                <td >{{Arr::get($reservation,'partner')}}</td>
                                <td >{{Arr::get($reservation,'voucher_date')}}</td>
                                <td >{{Arr::get($reservation,'sales_agent')}}</td>
                                <td >{{Arr::get($reservation,'selling_place')}}</td>
                                <td >{{Arr::get($reservation,'id')}}</td>
                                <td >{{Arr::get($reservation,'name')}}</td>
                                <td >{{Arr::get($reservation,'procedure')}}</td>
                                <td >{{Arr::get($reservation,'adults')}}</td>
                                <td >{{Arr::get($reservation,'children')}}</td>
                                <td >{{Arr::get($reservation,'price_eur')}}</td>
                                <td align="center" >{{Arr::get($reservation,'invoice_charge')}}</td>
                                <td align="center" >{{Arr::get($reservation,'commission_amount')}}</td>
                                <td align="center" >{{Arr::get($reservation,'commission')}} %</td>
                                <td >Transfer</td>
                        @endif

                    </tr>
                @endforeach
                </tbody>
            </table>

        </x-card>
    @else




    @endif

</div>
