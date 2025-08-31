<div class=" my-4">

    <!-- If Reservation Is Created -->
    @if($reservation)
        <p class="text-right">Reservation Saved to the Transfer Application - ID:{{$reservation->id}}<x-icon name="check-circle" solid class="w-6 h-6  ml-4  text-positive-400 float-right" ></x-icon></p>
    @else
        <p class="text-right">Reservation Not Saved To Transfer Application <x-icon name="exclamation" solid class="w-6 h-6  ml-4  text-red-400 float-right" ></x-icon></p>
    @endif

    <!-- If Reservation is Included In Accommodation -->
    @if($reservation->included_in_accommodation_reservation == 1)

        @if(!$reservation->isVLevelReservation())
            <p class="text-right">No Opera Download Needed<x-icon name="check-circle" solid class="w-6 h-6  ml-4  text-positive-400 float-right" ></x-icon></p>
            <small class="flex justify-end">Reservation Included in Accommodation Reservation</small>
        @endif
    @endif

    @if($reservation->isVLevelReservation())
        <!-- Checking Opera Download -->
        @if($reservation->isSyncedWithOpera() == 1)
            <p class="text-right">Reservation Alert Synced With Opera<x-icon name="check-circle" solid class="w-6 h-6  ml-4  text-positive-400 float-right" ></x-icon></p>
        @else
            <p class="text-right">Reservation Alert Not Synced With Opera<x-icon name="exclamation" solid class="w-6 h-6  ml-4  text-red-400 float-right"></x-icon></p>
        @endif

    @elseif($reservation->isVLevelReservation() === false && $reservation->included_in_accommodation_reservation == 0)
        <!-- Checking Opera Download -->
        @if($reservation->isSyncedWithOpera() == 1)
                <p class="text-right">Reservation Downloaded To Opera<x-icon name="check-circle" solid class="w-6 h-6  ml-4  text-positive-400 float-right" ></x-icon></p>
            @else
                <p class="text-right">Reservation Not Downloaded To Opera<x-icon name="exclamation" solid class="w-6 h-6  ml-4  text-red-400 float-right"></x-icon></p>
        @endif

    @endif

    @if($reservation->ConfirmationSentMailToGuest())
        <p class="text-right">E-mail sent to the guest <x-icon name="check-circle" solid class="w-6 h-6  ml-4  text-positive-400 float-right" ></x-icon></p>
    @else
        <p class="text-right">E-mail not sent to the guest <x-icon name="exclamation" solid class="w-6 h-6  ml-4  text-red-400 float-right" ></x-icon></p>
    @endif

    @if($reservation->ConfirmationSentMailToPartner())
        <p class="text-right">E-mail sent to the partner <x-icon name="check-circle" solid class="w-6 h-6  ml-4  text-positive-400 float-right" ></x-icon></p>
    @else
        <p class="text-right">E-mail not sent to the partner <x-icon name="exclamation" solid class="w-6 h-6  ml-4  text-red-400 float-right" ></x-icon></p>
    @endif

    <br/>

    <div class="flex justify-end">
        <x-button label="View Reservation Details" positive wire:click="showReservation()"/>

        @if(!$review)
            <x-button label="Add New Booking"  wire:click="newBooking()"/>
            <x-button label="Close" negative wire:click="close"/>
        @endif

    </div>


</div>
