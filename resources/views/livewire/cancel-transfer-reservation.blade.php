<div>
    <div class=" my-4">
        <p class="text-lg"> Are you sure you want to cancel this reservation?</p>
        <div class="divider"></div>
        @if($reservation->is_main)
            <div class="form-control mb-4">
                <label class="label cursor-pointer">
                    <span class="label-text">Cancel Round Trip?</span>
                    <input type="checkbox" class="checkbox" wire:model="cancelRoundTrip" />
                </label>
            </div>
        @endif


        <button class="btn btn-success text-white float-right" wire:click="cancelReservation">
            Save
        </button>
        <button class="btn btn-outline  float-right mx-2" wire:click="close">
            Cancel
        </button>

    </div>
</div>
