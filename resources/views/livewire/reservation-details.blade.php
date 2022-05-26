<div x-data="reservationSettings">


        @if($this->editReservation)
            <x-ez-modal isOpen>
            <livewire:edit-transfer-reservation :reservation="$this->editReservation"/>

        </x-ez-modal>
        @endif

        @if($this->cancelReservation)
            <x-ez-modal isOpen>
                <livewire:cancel-transfer-reservation :reservation="$this->cancelReservation"/>
            </x-ez-modal>
        @endif


    <x-ez-card class=" flex-grow mb-2">


        <x-slot name="body">
            <div class="flex items-center justify-between">
                <div>
                    <p class=" text-xl font-bold">Transfer #{{$reservation->id}}: </p>
                    <p><strong>Created by:</strong> {{$reservation->createdBy->name}}
                        <strong>@</strong> {{ $reservation->created_at->format('d.m.Y H:i') }}</p>
                    @if($reservation->updated_by)
                        <p><strong>Updated by:</strong> {{$reservation->updatedBy->name}}
                            <strong>@</strong> {{ $reservation->updated_at->format('d.m.Y H:i') }}</p>
                    @endif
                    @if($reservation->is_round_trip)
                        <span class="badge  badge-success">Round trip</span>
                    @endif

                </div>

                <a class="btn btn-outline " href="{{route('bookings')}}"><i class="fas fa-angle-left mr-2"></i> Back</a>
            </div>
        </x-slot>
    </x-ez-card>

    <div class="divider"></div>
    <div class="tabs ">
        <a class="tab tab-lifted tab-lg flex-grow" :class="{ 'tab-active': tab === 'reservation' }"
           x-on:click.prevent="tab = 'reservation'" href="#">
            <button x-show="tab === 'reservation'"
                    class="btn btn-outline btn-error  btn-sm absolute left-2"
                    wire:click="openCancelModal({{$reservation->id}})"
            ><i class="fas fa-times"></i></button>
            <strong>Reservation</strong>
            <button x-show="tab === 'reservation'" class="btn btn-outline  float-right btn-sm absolute right-2"
                wire:click="openUpdateModal({{$reservation->id}})"
            ><i class="fas fa-pen"></i></button>
        </a>
        @if($reservation->is_round_trip)

            <a class="tab tab-lifted tab-lg flex-grow" :class="{ 'tab-active': tab === 'round-trip-reservation' }"
               x-on:click.prevent="tab = 'round-trip-reservation'" href="#">

                <button x-show="tab === 'round-trip-reservation'"
                        class="btn btn-outline btn-error btn-sm absolute left-2"
                        wire:click="openCancelModal({{$reservation->returnReservation->id}})"
                ><i class="fas fa-times"></i></button>

                <strong>Round Trip Reservation</strong>

                <button x-show="tab === 'round-trip-reservation'"
                        class="btn btn-outline  btn-sm absolute right-2"
                        wire:click="openUpdateModal({{$reservation->returnReservation->id}})"
                ><i class="fas fa-pen"></i></button>

            </a>
        @endif
    </div>
    <div class="bg-base-100 p-2 border-b border-l border-r rounded-b-box mb-20" style="border-color: #136baa;
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
</div>
