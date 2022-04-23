<div class="internal-reservation container ">
    <div class="grid grid-cols-3 gap-4">
        <div class="col-span-2 ">
            @if($step === 1)
                <x-ez-card class="mb-4 ">

                    <x-slot name="title">
                        <i class="fas fa-search"></i> Search
                    </x-slot>

                    <x-slot name="body" class="">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="form-control ">
                                <select class="my-select select-sm" wire:model="stepOneFields.destinationId">
                                    <option value="">Pick a destination</option>
                                    @foreach(\App\Models\Destination::all() as $destination)
                                        <option value="{{$destination->id}}">{{$destination->name}}</option>

                                    @endforeach


                                </select>

                            </div>

                            <div class="form-control ">
                                @if(!empty($this->stepOneFields['destinationId']))
                                    @if($this->startingPoints->isNotEmpty())
                                        <select class="my-select select-sm" wire:model="stepOneFields.startingPointId">
                                            <option value="">Pickup location</option>
                                            @foreach($this->startingPoints as $point)
                                                <option value="{{$point->id}}">{{$point->name}}</option>
                                            @endforeach

                                        </select>
                                    @else
                                        <div class="alert alert-warning">No Pickup points for that destination</div>
                                    @endif
                                @endif
                            </div>
                            @if(!empty($this->stepOneFields['startingPointId']))

                                <div class="form-control ">
                                    @if($this->endingPoints->isNotEmpty())
                                        <select class="my-select select-sm" wire:model="stepOneFields.endingPointId">
                                            <option value="">Drop off location</option>
                                            @foreach($this->endingPoints as $point)
                                                <option value="{{$point->id}}">{{$point->name}}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <div class="alert alert-warning">No Dropoff points for that pickup point!</div>

                                    @endif
                                </div>
                            @endif

                        </div>
                        @if(!empty($stepOneFields['destinationId']) && !empty($stepOneFields['startingPointId']) && !empty($stepOneFields['endingPointId']))
                            <div x-data="{open: false}" x-show="open" x-transition
                                 x-init="setTimeout(() => { open = true })">


                                <div class="divider my-1    "></div>
                                <div class="gap-2">
                                    <div class="grid grid-cols-4  gap-2">
                                        <div class="form-control  ">
                                            <label class="label">
                                                <span class="label-text">Date:</span>
                                            </label>
                                            <input x-init="
                                        flatpickr($el, {
                                        disableMobile: 'true',
                                        minDate:'today',
                                        dateFormat:'d.m.Y',
                                        defaultDate:'{{$stepOneFields['dateTo']}}'});
                                        " readonly
                                                   wire:model="stepOneFields.dateTo"
                                                   class="ml-2 input input-bordered input-sm mt-2"
                                                   placeholder="Date to:">

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
                                        time_24hr: true,
                                        defaultDate:"{{$stepOneFields['timeTo']}}"});
                                        ' readonly
                                                   wire:model="stepOneFields.timeTo"
                                                   class="ml-2 input input-bordered input-sm mt-2"
                                                   placeholder="Time to:">

                                        </div>

                                        @if($twoWay)


                                            <div class="form-control  ">
                                                <label class="label">
                                                    <span class="label-text">Date:</span>
                                                </label>
                                                <input x-init="
                                        flatpickr($el, {
                                        disableMobile: 'true',
                                        minDate:'today',
                                        dateFormat:'d.m.Y',
                                        defaultDate:'{{$stepOneFields['dateFrom']}}'});
                                        " readonly
                                                       wire:model="stepOneFields.dateFrom"
                                                       class="ml-2 input input-bordered input-sm mt-2"
                                                       placeholder="Date from:">

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
                                        time_24hr: true,

                                        defaultDate:"{{$stepOneFields['timeFrom']}}"});
                                        ' readonly
                                                       wire:model="stepOneFields.timeFrom"
                                                       class="ml-2 input input-bordered input-sm mt-2"
                                                       placeholder="Time from:">

                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-control rounded ">
                                        <label class="label cursor-pointer bg-gray-100 rounded mt-6 mb-1  ">
                            <span class="label-text">
                              <i class="fas fa-exchange-alt mx-4"></i>
                                 Two way</span>
                                            <input type="checkbox" wire:model="twoWay" class="checkbox">
                                        </label>

                                    </div>

                                </div>


                                <div class="divider my-1    "></div>

                                <div class="">
                                    <div class="flex flex-wrap gap-2">

                                        <div class="form-control  ">
                                            <label class="label">
                                                <span class="label-text">Senior:</span>
                                            </label>
                                            <input class="my-input input-sm w-full" placeholder=""
                                                   wire:model="stepOneFields.seniors">

                                        </div>
                                        <div class="form-control  ">
                                            <label class="label">
                                                <span class="label-text">Adult:</span>
                                            </label>
                                            <input class="my-input input-sm w-full" placeholder=""
                                                   wire:model="stepOneFields.adults">

                                        </div>


                                        <div class="form-control  ">
                                            <label class="label">
                                                <span class="label-text">Child(3-17):</span>
                                            </label>
                                            <input class="my-input input-sm w-full" placeholder=""
                                                   wire:model="stepOneFields.children">

                                        </div>
                                        <div class="form-control  ">
                                            <label class="label">
                                                <span class="label-text">Infant(0-2):</span>
                                            </label>
                                            <input class="my-input input-sm w-full" placeholder=""
                                                   wire:model="stepOneFields.infants">

                                        </div>
                                        <div class="form-control  ">
                                            <label class="label">
                                                <span class="label-text">Luggage</span>
                                            </label>
                                            <input class="my-input input-sm w-1/2" placeholder=""
                                                   wire:model="stepOneFields.luggage">

                                        </div>
                                    </div>


                                </div>
                            </div>
                        @endif
                    </x-slot>
                </x-ez-card>

            @endif



            @if($step === 1)

                @if($this->availableTransfers->isNotEmpty() && !empty($stepOneFields['destinationId']) && !empty($stepOneFields['startingPointId']) && !empty($stepOneFields['endingPointId']))
                    <div x-data="{open: false}" x-show="open" x-transition
                         x-init="setTimeout(() => { open = true })">

                        <x-ez-card class="">
                            <x-slot name="title">Transfers</x-slot>
                            <x-slot name="body">
                                @foreach($this->availableTransfers as $transfer)
                                    <div class="card  bg-gray-200 ">
                                        <div class="card-body p-2">

                                            <div class="flex gap-4">
                                                <div class="basis-1/5">
                                                    <img class="h-24 w-full object-cover rounded-xl"
                                                         src="{{$transfer->primaryImageUrl}}"/>
                                                </div>
                                                <div class="basis-4/5">
                                                    <h2 class="card-title mb-2">{{$transfer->name}}</h2>
                                                    <div class="flex gap-4 mb-2">
                                                        <span class=" ">Type: Van</span>
                                                        <span class=" ">Max. Occ: {{$transfer->vehicle->max_occ}}</span>
                                                        <span class=" ">Max. Luggage:{{$transfer->vehicle->max_luggage}}</span>

                                                    </div>z
                                                    <span class="  ">Price: <b> {{Cknow\Money\Money::EUR($transfer->pivot->price)}} EUR</b></span>
                                                    <div class="badge badge-info top-2 right-2 absolute">
                                                        {{$transfer->partner->name}}
                                                    </div>
                                                    <button
                                                        class="btn btn-sm btn-primary absolute bottom-2 rounded-xl right-2"
                                                        wire:click="selectTransfer({{$transfer->id}})">Select
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach


                            </x-slot>
                        </x-ez-card>
                    </div>
                @endif
            @endif


            @if($step === 2)
                <div>
                    <x-ez-card class="mb-4">
                        <x-slot name="title">Transfer details</x-slot>
                        <x-slot name="body">
                            <div class="grid grid-cols-3 gap-4">

                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Arrival flight number"
                                                          value="31782563"></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Arrival date"
                                                          value="01.01.2022"></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm value="12:30"
                                                          label="Time of arrival"></x-form.ez-text-input>

                                </div>

                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Departure flight number"
                                                          value="1872351"></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Departure date"
                                                          value="07.01.2022"></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm value="14:30"
                                                          label="Time of departure"></x-form.ez-text-input>

                                </div>

                                <div class="col-span-1">
                                    <x-form.ez-text-input sm value="14:30" label="Pickup time"></x-form.ez-text-input>
                                </div>

                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Remark"></x-form.ez-text-input>
                                </div>
                            </div>

                        </x-slot>
                    </x-ez-card>
                    <x-ez-card class="mb-4">
                        <x-slot name="title">Child seats</x-slot>
                        <x-slot name="body">
                            <div class="grid grid-cols-4 gap-4">
                                @foreach($seats as $seat)
                                    <div class="col-span-2">
                                        <x-form.ez-select model="" :label="'Seat #'.$loop->index +1 .':'"
                                                          :items="['Booster (10-15 kg)','Egg (0-5 kg)','Classic(5-10 kg)']"
                                                          sm="true"></x-form.ez-select>
                                    </div>


                                @endforeach

                            </div>
                            <div class="flex justify-end gap-4">

                                <button class="btn btn-outline  btn-sm btn-circle" wire:click="addSeat"><i
                                        class="fas fa-plus"></i></button>
                                <button class="btn btn-outline  btn-sm btn-circle" wire:click="removeSeat"><i
                                        class="fas fa-minus"></i></button>
                            </div>

                        </x-slot>
                    </x-ez-card>
                    <x-ez-card class="mb-4">
                        <x-slot name="title">
                            <div class="flex justify-between w-full">
                                <span>                              Lead traveller details

                                </span>
                                <div>
                                    <x-ez-modal>
                                        <x-slot name="button" class="btn-sm">Pull traveller</x-slot>

                                        <x-form.ez-text-input sm label="Reservation id"
                                                              value="3127863"></x-form.ez-text-input>
                                        <x-slot name="footer">
                                            <label for="ez-modal" wire:click="pullTraveller"
                                                   class="btn btn-sm btn-success">
                                                Pull data
                                            </label>
                                        </x-slot>

                                    </x-ez-modal>
                                </div>
                            </div>

                        </x-slot>
                        <x-slot name="body">
                            <div class="grid grid-cols-3 gap-4">

                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Title"
                                                          value="{{$fakeData['title']}}"></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="First name"
                                                          value="{{$fakeData['fName']}}"></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Last name"
                                                          value="{{$fakeData['lName']}}"></x-form.ez-text-input>

                                </div>

                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Reservation number"
                                                          value="{{$fakeData['resNum']}}"></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Email"
                                                          value="{{$fakeData['email']}}"></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Phone"
                                                          value="{{$fakeData['phone']}}"></x-form.ez-text-input>

                                </div>


                            </div>

                        </x-slot>
                    </x-ez-card>
                    <x-ez-card>
                        <x-slot name="title">Other traveller details</x-slot>
                        <x-slot name="body">
                            <div class="grid grid-cols-4 gap-4">
                                @foreach($travellers as $traveler)
                                    <div class="col-span-1">
                                        <x-form.ez-text-input sm label="Title" value="Mr."></x-form.ez-text-input>
                                    </div>
                                    <div class="col-span-1">
                                        <x-form.ez-text-input sm label="First name"
                                                              value="John "></x-form.ez-text-input>
                                    </div>
                                    <div class="col-span-1">
                                        <x-form.ez-text-input sm label="Last name" value="Doe"></x-form.ez-text-input>
                                    </div>

                                    <div class="col-span-1">
                                        <x-form.ez-text-input sm label="Comment" value=""></x-form.ez-text-input>
                                    </div>

                                @endforeach

                            </div>
                            <div class="flex justify-end gap-4">

                                <button class="btn btn-outline  btn-sm btn-circle" wire:click="addTraveller"><i
                                        class="fas fa-plus"></i></button>
                                <button class="btn btn-outline  btn-sm btn-circle" wire:click="removeTraveller"><i
                                        class="fas fa-minus"></i></button>
                            </div>

                        </x-slot>
                    </x-ez-card>
                </div>

            @endif
        </div>
        <div class="col-span-1 ">
            <x-ez-card class="sticky" style="top:5vh">
                <x-slot name="title">Reservation details</x-slot>
                <x-slot name="body">

                    <div class="divider my-1    "></div>
                    <div class="res-details">
                        <p><span>From:</span> <b>Poreč</b></p>
                        <p>To: <b>Rijeka airport</b></p>
                        <p>Departure: <b>Nov 06, 2022</b></p>
                        <p>Passengers: <b>3</b></p>
                        <p>Ticket type: <b>One way</b></p>
                    </div>
                    <div class="divider my-1    "></div>

                    <div class="alert alert-info alert-sm ">
                        <div class="text-right ml-auto text-white gap-2 pr-2">
                            Total price: <b> 1,233 EUR</b>
                        </div>
                    </div>
                </x-slot>
            </x-ez-card>

        </div>

    </div>


</div>
