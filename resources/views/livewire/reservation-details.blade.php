<div x-data="reservationSettings">
    <x-card class=" flex-grow mb-2">
        <div class="flex items-center justify-between">
            <div>
                <p  class="text-sm font-bold"><u>Transfer Details #{{$reservation->id}}</u>
                </p>
                <p  class="text-sm"><b>Route: </b>{{$reservation->pickupAddress->name}} => {{$reservation->dropoffAddress->name}}
                </p>
                <strong class=" text-sm">Created by: {{$reservation->createdBy->name}}</strong>
                <strong class=" text-sm">@ {{\Carbon\Carbon::parse($reservation->created_at->format('d.m.Y H:i'))->format('d.m.Y H:i')}}</strong>
                @if($reservation->updated_by)
                    <br/>
                    @if($reservation->getOverallReservationStatus() == 'cancelled')
                        <strong class=" text-sm">Cancelled by: {{$reservation->updatedBy->name}}</strong>
                    @else
                        <strong class=" text-sm">Updated by: {{$reservation->updatedBy->name}}</strong>
                    @endif

                        <strong class=" text-sm">@ {{ $reservation->updated_at->format('d.m.Y H:i') }}</strong>
                @endif

                <br/>

                @if($reservation->included_in_accommodation_reservation == 1)
                    <span class="font-extrabold text-info text-sm">Reservation included in Accommodation Reservation<br/><small><ul><li><i> - Reservation Not posted to Opera.</i>  </li></ul></small></span>
                    <!-- Invoice Details -->
                    @if($reservation->getInvoiceData('invoice_number') != '-')
                        <span class="font-extrabold text-info text-sm">Invoice: <span class="text-info font-normal">{{gmdate('Y').'-'.$reservation->getInvoiceData('invoice_number')}} ({{$reservation->getInvoiceData('amount')}})</span></span>
                        <span class="font-extrabold text-info text-sm">ZKI: <span class="text-info font-normal">{{$reservation->getInvoiceData('zki')}}</span></span>
                        <span class="font-extrabold text-info text-sm">JIR: <span class="text-info font-normal">{{$reservation->getInvoiceData('jir')}}</span></span>
                    @endif

                    @if($reservation->getInvoiceData('invoice_number') == '-')
                        <x-button xs icon="external-link" wire:click="openFiskalSyncModal({{$reservation->id}})">Issue Invoice ( Fiskalizacija )</x-button>
                    @endif

                @elseif($reservation->v_level_reservation == 1)
                    <span class="font-extrabold text-info text-sm">V Level Rate Plan Reservation included in Accommodation Reservation<br/><small>
                            <ul>
                                <li><i> - Reservation Not posted to Opera.</i><br/>
                                    <i> - Reservation Invoice ( fiskalizacija popratnog dokumenta ) Not Created via Transfer App.</i></li>
                                    <li> - Connected document not sent to Opera</li>
                            </ul>
                        </small>
                    </span>
                @else
                    <span class="font-extrabold text-info text-sm">Opera Status: {{$reservation->isSyncedWithOpera()?'Synced':'Not Synced'}}</span>
                    <x-button primary xs wire:click="openOperaSyncModal({{$reservation->id}})">{{$reservation->isSyncedWithOpera()?'Re-Sync':'Sync'}}</x-button>
                    <x-button xs icon="external-link" wire:click="openOperaSyncLogModal({{$reservation->id}})">View Sync Log</x-button>
                    <br/>
                    @if($reservation->included_in_accommodation_reservation != 1 && $reservation->v_level_reservation != 1)
                        @if($reservation->getInvoiceData('invoice_number') != '-')
                            <!-- Invoice Details -->
                            <span class="font-extrabold text-info text-sm">Invoice: <span class="text-info font-normal">{{gmdate('Y').'-'.$reservation->getInvoiceData('invoice_number')}} ({{$reservation->getInvoiceData('amount')}})</span></span>
                            <span class="font-extrabold text-info text-sm">ZKI: <span class="text-info font-normal">{{$reservation->getInvoiceData('zki')}}</span></span>
                            <span class="font-extrabold text-info text-sm">JIR: <span class="text-info font-normal">{{$reservation->getInvoiceData('jir')}}</span></span>
                        @endif
                        @if($reservation->getInvoiceData('invoice_number') == '-')
                            <span class="font-extrabold text-info text-sm">Invoice not issued: <span class="text-info font-normal">{{$reservation->getLatestInvoiceError()}}</span></span>
                            <x-button xs icon="external-link" wire:click="openFiskalSyncModal({{$reservation->id}})">Issue Invoice ( Fiskalizacija )</x-button>
                        @endif
                    @endif

                @endif

                #Main Direction
                @if($reservation->status == 'cancelled')

                    <br/>
                    <br/>
                    <p  class="text-sm font-bold" style="color:red"><u>{{$reservation->pickupLocation->name}} -> {{$reservation->dropoffLocation->name}} Cancellation Details #{{$reservation->id}}</u>
                    </p>
                    <p class="text-sm"><b>Cancellation DateTime: </b>{{$reservation->cancelled_at}}</p>
                    @if($reservation->hasCancellationFee() > 0)

                        @if($reservation->included_in_accommodation_reservation != 1 && $reservation->v_level_reservation != 1)
                            <span class="font-extrabold text-info text-sm">Cancellation Fee Applied: </span>{{$reservation->cancellation_fee}} € ( {{$reservation->cancellation_type}}) <br/></span>
                            <!-- Cancellation Invoice Details -->
                            <span class="font-extrabold text-info text-sm">Invoice: <span class="text-info font-normal">{{gmdate('Y').'-'.$reservation->getInvoiceData('invoice_number','cancellation_fee')}} ({{$reservation->getInvoiceData('amount','cancellation_fee')}})</span></span>
                            <span class="font-extrabold text-info text-sm">ZKI: <span class="text-info font-normal">{{$reservation->getInvoiceData('zki','cancellation_fee')}}</span></span>
                            <span class="font-extrabold text-info text-sm">JIR: <span class="text-info font-normal">{{$reservation->getInvoiceData('jir','cancellation_fee')}}</span></span>
                        @else
                            <span class="font-extrabold text-info text-sm">Cancellation Fee Applied: </span>{{$reservation->cancellation_fee}} € ( {{$reservation->cancellation_type}}  - sent as 0.00€ to Opera) <br/></span>
                        @endif
                        <x-button xs icon="external-link" wire:click="openOperaSyncLogModal({{$reservation->id}})">View Sync Log</x-button>

                    @else
                        <p class="text-sm"><b>Cancellation Fee:</b> No cancellation fee applied</p>
                    @endif
                @endif

                @if($reservation->cf_null != 1)
                    @if($reservation->isDocumentConnectedSync() && $reservation->getInvoiceData('jir'))
                        <b class="text-sm">Connected Document Synced with Opera: </b>{{$reservation->getInvoiceData('invoice_number')}}</b>
                        <br/><br/>
                    @else
                        <b class="text-sm">Connected Document not Synced with Opera: </b><x-button primary xs wire:click="openDocumentSyncModal({{$reservation->id}})">Re-try</x-button>
                        <br/><br/>
                    @endif
                @endif

                @if($reservation->isRoundTrip && $reservation->returnReservation->status == 'cancelled')

                    <br/>
                    <p  class="text-sm font-bold" style="color:red"><u>{{$reservation->returnReservation->pickupLocation->name}} -> {{$reservation->returnReservation->dropoffLocation->name}} Cancellation Details #{{$reservation->returnReservation->id}}</u>
                    </p>
                    <p class="text-sm"><b>Cancellation DateTime: </b>{{$reservation->returnReservation->cancelled_at}}</p>
                    @if($reservation->returnReservation->hasCancellationFee() > 0)


                        @if($reservation->returnReservation->included_in_accommodation_reservation != 1 && $reservation->returnReservation->v_level_reservation != 1)
                            <span class="font-extrabold text-info text-sm">Cancellation Fee Applied: </span>{{$reservation->returnReservation->cancellation_fee}} € ( {{$reservation->returnReservation->cancellation_type}} ) <br/></span>
                            <!-- Cancellation Invoice Details -->
                            <span class="font-extrabold text-info text-sm">Invoice: <span class="text-info font-normal">{{gmdate('Y').'-'.$reservation->getInvoiceData('invoice_number','cancellation_fee')}} ({{$reservation->getInvoiceData('amount','cancellation_fee')}})</span></span>
                            <span class="font-extrabold text-info text-sm">ZKI: <span class="text-info font-normal">{{$reservation->getInvoiceData('zki','cancellation_fee')}}</span></span>
                            <span class="font-extrabold text-info text-sm">JIR: <span class="text-info font-normal">{{$reservation->getInvoiceData('jir','cancellation_fee')}}</span></span>
                        @else
                            <span class="font-extrabold text-info text-sm">Cancellation Fee Applied: </span>{{$reservation->returnReservation->cancellation_fee}} € ( {{$reservation->returnReservation->cancellation_type}} - sent as 0.00€ to Opera ) <br/></span>
                        @endif
                            <x-button xs icon="external-link" wire:click="openOperaSyncLogModal({{$reservation->id}})">View Sync Log</x-button>
                    @else
                        <p class="text-sm"><b>Cancellation Fee:</b> No cancellation fee applied</p>
                    @endif
                @endif

                @if($reservation->getOverallReservationStatus() == 'cancelled')
                <button success class="ds-btn  ds-btn-xs"
                     wire:loading.class="ds-loading"
                     wire:target="downloadCancellationPDF"
                     wire:click="downloadCancellationPDF({{$reservation->id}})">

                        Download Cancellation
                        <x-icon name="document-download" wire:loading.remove wire:target="downloadCancellationPDF({{$reservation->id}})" class="w-4 h-4 ml-2"> </x-icon>
                </button>
                    @else

                        <button success class="ds-btn  ds-btn-xs"
                                wire:loading.class="ds-loading"
                                wire:target="downloadConfirmationPDF"
                                wire:click="downloadConfirmationPDF({{$reservation->id}})">
                        Download Confirmation
                        <x-icon name="document-download" wire:loading.remove wire:target="downloadConfirmationPDF({{$reservation->id}})" class="w-4 h-4 ml-2"> </x-icon>
                        </button>
                    @endif

                @if($reservation->isCancelled() && $reservation->hasCancellationFee() || ($reservation->isRoundTrip && $reservation->returnReservation->status == 'cancelled' && $reservation->returnReservation->hasCancellationFee()))

                    @if($reservation->cf_null != 1)
                        <button success class="ds-btn  ds-btn-xs"
                                wire:loading.class="ds-loading"
                                wire:target="downloadCFPDF"
                                wire:click="downloadCFPDF({{$reservation->id}})">
                            Cancellation Fee
                            <x-icon name="document-download" wire:loading.remove wire:target="downloadCFPDF({{$reservation->id}})" class="w-4 h-4 ml-2">
                            </x-icon>
                        </button>
                    @endif

                @endif

                <button success class="ds-btn  ds-btn-xs"
                        wire:loading.class="ds-loading"
                        wire:target="downloadVoucher"
                        wire:click="downloadVoucher({{$reservation->id}})">
                    Download Voucher
                    <x-icon name="document-download" wire:loading.remove wire:target="downloadVoucher({{$reservation->id}})" class="w-4 h-4 ml-2">
                    </x-icon>
                </button>
                <br/><br/>
                <x-button success xs wire:click="openReservationStatusModal({{$reservation->id}})">View Status Breakdown</x-button>
            </div>

            <x-button href="{{route('bookings')}}"><i class="fas fa-angle-left mr-2"></i> Back</x-button>
        </div>
    </x-card>

    <div class="ds-divider"></div>
    <div class="ds-tabs ">
        <a class="ds-tab ds-tab-lifted  ds-tab-lg flex-grow "
            style="border-color: #136baa;"
           :class="{ 'ds-tab-active': tab === 'reservation' }"
           x-on:click.prevent="tab = 'reservation'" href="#">
            @if(!$reservation->isCancelled())

                <x-button x-show="tab === 'reservation'"
                          icon="x"
                          class=" absolute left-2"
                          wire:click="openCancelModal({{$reservation->id}})"
                >Cancel \ No Show</x-button>
            @endif

            <strong>Reservation</strong>
            @if(!$reservation->isCancelled())

                <x-button x-show="tab === 'reservation'" class="absolute right-2"
                          wire:click="openUpdateModal({{$reservation->id}})"
                          icon="pencil"
                />
            @endif

        </a>
        @if($reservation->is_round_trip && $reservation->returnReservation)

            <a class="ds-tab ds-tab-lifted ds-tab-lg flex-grow"
               style="border-color: #136baa;"
               :class="{ 'ds-tab-active': tab === 'round-trip-reservation' }"
               x-on:click.prevent="tab = 'round-trip-reservation'" href="#">
                @if(!$reservation->returnReservation->isCancelled())
                    <x-button x-show="tab === 'round-trip-reservation'"
                              class=" absolute left-2"
                              icon="x"
                              wire:click="openCancelModal({{$reservation->returnReservation->id}})"
                    >Cancel \ No Show</x-button>
                @endif

                <strong>Round Trip Reservation</strong>
                @if(!$reservation->returnReservation->isCancelled())

                    <x-button x-show="tab === 'round-trip-reservation'"
                              class=" absolute right-2"
                              icon="pencil"
                              wire:click="openUpdateModal({{$reservation->returnReservation->id}})"
                    />
                @endif

            </a>
        @endif
    </div>
    <div class=" p-2 border-b border-l border-r rounded-b-box mb-20 " style="border-color: #136baa;
">
        <div x-show="tab === 'reservation'">
            <livewire:reservation-view :reservation="$reservation"/>
        </div>
        @if($reservation->is_round_trip)

            <div x-show="tab === 'round-trip-reservation'">
                <livewire:reservation-view :reservation="$reservation->returnReservation"/>
            </div>
        @endif
    </div>


    <script>
        function reservationSettings() {
            return {
                tab: 'reservation',
            }
        }
    </script>

    @if($operaSyncLogModal)
    <x-modal.card wire:model="operaSyncLogModal" lg max-width="5xl" title="Opera Reservation Sync Log - Reservation ID#{{$this->reservation->id}}">
        <livewire:sync-opera-transfer-reservation-log :reservation="$this->reservation"/>
    </x-modal.card>
    @endif

    @if($documentSyncModal)
    <x-modal.card wire:model="documentSyncModal" lg max-width="5xl" title="Opera Document Sync - Reservation ID#{{$this->reservation->id}}">
        <livewire:sync-document-reservation :reservation="$this->reservation"/>
    </x-modal.card>
    @endif

    @if($operaSyncModal)
    <x-modal.card wire:model="operaSyncModal" title="Sync reservation #{{$this->reservation->id}} with Opera" >
        <livewire:sync-opera-transfer-reservation :reservation="$this->reservation"/>
    </x-modal.card>
    @endif

    @if($editReservation)
        <x-modal.card wire:model="editModal"  title="Editing reservation #{{$this->editReservation->id}}">
            <livewire:edit-transfer-reservation :reservation="$this->editReservation"/>
        </x-modal.card>
    @endif

    @if($cancelReservation)

        <x-modal.card wire:model="cancelModal" title="Cancel reservation #{{$this->cancelReservation->id}}">
            <livewire:cancel-transfer-reservation :reservation="$this->cancelReservation" :partnerID="$this->reservation->partner_id"/>
        </x-modal.card>
    @endif

    @if($fiskalSyncModal)

        <x-modal.card wire:model="fiskalSyncModal" title="Issue Invoice for reservation">
            <livewire:issue-reservation-invoice :reservation="$this->reservation"/>
        </x-modal.card>

    @endif

    @if($reservationStatusModal)
        <x-modal.card wire:model="reservationStatusModal"  title="Reservation Status Breakdown #{{$this->reservation->id}}">
            <livewire:show-reservation-status  :reservation="$this->reservation" :review="true" />
        </x-modal.card>
    @endif

</div>
