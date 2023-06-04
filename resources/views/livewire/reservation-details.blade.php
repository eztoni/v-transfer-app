<div x-data="reservationSettings">
    <x-card class=" flex-grow mb-2">
        <div class="flex items-center justify-between">
            <div>
                <p class=" text-xl font-bold">Transfer #{{$reservation->id}}: </p>
                <p><strong>Created by:</strong> {{$reservation->createdBy->name}}
                    <strong>@</strong> {{\Carbon\Carbon::parse($reservation->created_at->format('d.m.Y H:i'))->addHour()->format('d.m.Y H:i')}}</p>
                @if($reservation->updated_by)
                    <p><strong>Updated by:</strong> {{$reservation->updatedBy->name}}
                        <strong>@</strong> {{ $reservation->updated_at->format('d.m.Y H:i') }}</p>
                @endif
                @if($reservation->is_round_trip)
                    <span class="ds-badge  ds-badge-success">Round trip</span>
                @endif
                <p><span class="font-extrabold text-info">Opera Status: {{$reservation->isSyncedWithOpera()?'Synced':'Not Synced'}}<br/></span>
                    <x-button primary wire:click="openOperaSyncModal({{$reservation->id}})">{{$reservation->isSyncedWithOpera()?'Re-Sync':'Sync'}}</x-button>
                    <x-button sm icon="external-link" wire:click="openOperaSyncLogModal({{$reservation->id}})">View Sync Log</x-button>
                    <br/>
                    <span class="font-extrabold text-info">&nbsp;Invoice: <span class="text-info font-normal">{{!empty($reservation->invoices[0]) ? $reservation->invoices[count($reservation->invoices)-1]->invoice_id.'-'.$reservation->invoices[0]->invoice_establishment.'-'.$reservation->invoices[0]->invoice_device : '-'}}</span></span>
                    <br/>
                    <span class="font-extrabold text-info">&nbsp;ZKI: <span class="text-info font-normal">{{!empty($reservation->invoices[0]) ? $reservation->invoices[0]?->zki:'-'}}</span></span>
                    <br/>
                    <span class="font-extrabold text-info">&nbsp;JIR: <span class="text-info font-normal">{{!empty($reservation->invoices[0]) ? $reservation->invoices[0]?->jir:'-'}}</span></span>

                @if(empty($reservation->invoices[0]))
                        <br/>
                        <x-button sm icon="external-link" wire:click="openFiskalSyncModal({{$reservation->id}})">Issue Invoice ( Fiskalizacija )</x-button>
                @endif

                @if($reservation->status == 'cancelled')
                    <br/>
                    <br/>
                    <p><b>Cancellation DateTime: </b>{{$reservation->cancelled_at}}</p>
                    @if($reservation->cancellation_fee > 0)
                    <p><b>Cancellation Fee Applied: </b>{{$reservation->cancellation_fee}} € ( {{$reservation->cancellation_type}} ) </p>
                    @else
                    <p><b>Cancellation Fee:</b> No cancellation fee applied</p>
                @endif
                @endif

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
        @if($reservation->is_round_trip)

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
                @if(!$reservation->isCancelled())

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

</div>
