<div>

    <h1 class="text-xl font-bold">Editing reservation #{{$reservation->id}}</h1>
    <div class="divider"></div>
    <div class="form-control  ">
        <label class="label">
            <span class="label-text">Date:</span>
        </label>
        <input x-init="
                                        flatpickr($el, {
                                        disableMobile: 'true',
                                        dateFormat:'d.m.Y'

                                        });
                                        " readonly
               wire:model="reservation.date"
               class=" input input-bordered input-sm mt-2             {{$reservation->isDirty('date')?'border-success':''}}
                   "
               placeholder="Date to:"
        >

        @error('reservation.date')
        <x-input-alert type='warning'>{{$message}}</x-input-alert>
        @enderror
    </div>
    <div class="form-control ">
        <label class="label">
            <span class="label-text">Time:</span>
        </label>
        <input x-init='
                                        flatpickr($el, {
                                        disableMobile: "true",
                                        enableTime: true,
                                        noCalendar: true,
                                        dateFormat: "H:i",
                                        time_24hr: true
                                     });
                                        ' readonly
               wire:model="reservation.time"
               class=" input input-bordered input-sm mt-2  {{$reservation->isDirty('time')?'border-success':''}}"
               placeholder="Time to:">
        @error('reservation.time')
        <x-input-alert type='warning'>{{$message}}</x-input-alert>
        @enderror
    </div>
    <x-form.ez-text-input :class="$reservation->isDirty('adults')?'border-success':''"
                          model="reservation.adults" sm
                          label="Adults"
    />
    <x-form.ez-text-input
        :class="$reservation->isDirty('children')?'border-success':''"
        model="reservation.children" sm
                          label="Children"
    />
    <x-form.ez-text-input
        :class="$reservation->isDirty('infants')?'border-success':''"
        model="reservation.infants" sm
                          label="Infants"
    />
    <x-form.ez-text-input
        :class="$reservation->isDirty('luggage')?'border-success':''"
        model="reservation.luggage" sm
                          label="Luggage"
    />
    <x-form.ez-text-input
        :class="$reservation->isDirty('flight_number')?'border-success':''"
        model="reservation.flight_number" sm
                          label="Flight number"
    />
    <div class="form-control">
        <label class="label">
            <span class="label-text">Remark:</span>
        </label>
        <textarea rows="1" wire:model="reservation.remark"
                  class="textarea textarea-bordered {{$reservation->isDirty('remark')?'border-success':''}}"
        ></textarea>
        @error('stepTwoFields.remark')
        <x-input-alert type='warning'>{{$message}}</x-input-alert>
        @enderror
    </div>


    <x-form.ez-select label="Send Modify Email"
                      :items="$this->sendEmailArray"
                      model="sendModifyMail" :show-empty-value="false"
                      sm="true"></x-form.ez-select>


    <div class=" my-4">

        <button class="btn btn-success text-white float-right" wire:click="save">
            Save
        </button>
        <button class="btn btn-outline  float-right mx-2" wire:click="cancel">
            Cancel
        </button>

    </div>
</div>
