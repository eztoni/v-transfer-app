<div class="internal-reservation">

    @if($step === 1)
        <x-ez-card class="mb-4 ">

            <x-slot name="title">
                Search
            </x-slot>

            <x-slot name="body" class="">
                <div class="flex justify-between gap-4">
                    <div class="form-control flex-grow basis-1/3">
                        <select class="my-select" wire:model="stepOneFields.destinationId">
                            <option value="">Pick a destination</option>
                            @foreach(\App\Models\Destination::all() as $destination)
                                <option value="{{$destination->id}}">{{$destination->name}}</option>

                            @endforeach


                        </select>

                    </div>

                    <div class="form-control flex-grow basis-1/3">
                        @if(!empty($this->stepOneFields['destinationId']))
                            <select class="my-select" wire:model="stepOneFields.pickupPointId">
                                <option value="">Pickup location</option>

                            </select>
                        @endif


                    </div>
                    <div class="form-control flex-grow basis-1/3">
                        @if(!empty($this->stepOneFields['destinationId']))

                            <select class="my-select" wire:model="stepOneFields.dropOffPointId">
                                <option value="">Drop off location</option>

                            </select>
                        @endif

                    </div>


                </div>
                <div class="divider my-1    "></div>
                <div class="flex justify-between gap-4">
                    <div class="basis-2/5  ">
                        <div class="flex justify-between gap-4">
                            <div class="form-control flex-grow ">
                                <label class="label">
                                    <span class="label-text">Date:</span>
                                </label>
                                <input class="my-input input-sm flex-grow" placeholder="" value="31.04.2022">

                            </div>
                            <div class="form-control flex-grow ">
                                <label class="label">
                                    <span class="label-text">Time:</span>
                                </label>
                                <input class="my-input input-sm flex-grow" placeholder="" value="22:45">

                            </div>
                        </div>
                        <div class="form-control rounded ">
                            <label class="label cursor-pointer bg-gray-100 rounded mt-6 mb-1  ">
                            <span class="label-text">
                              <i class="fas fa-exchange-alt mx-4"></i>
                                 Two way</span>
                                <input type="checkbox" checked="checked" class="checkbox">
                            </label>
                            <div class="flex justify-between gap-4">
                                <div class="form-control flex-grow ">
                                    <label class="label">
                                        <span class="label-text">Date (return):</span>
                                    </label>
                                    <input class="my-input input-sm flex-grow" placeholder="" value="31.04.2022">

                                </div>
                                <div class="form-control flex-grow ">
                                    <label class="label">
                                        <span class="label-text">Time (return):</span>
                                    </label>
                                    <input class="my-input input-sm flex-grow" placeholder="" value="22:45">

                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="divider divider-horizontal md:opacity-100 opacity-0"></div>

                    <div class="basis-3/5">
                        <div class="flex justify-between gap-4">

                            <div class="form-control flex-grow ">
                                <label class="label">
                                    <span class="label-text">Senior:</span>
                                </label>
                                <input class="my-input input-sm flex-grow" placeholder="" value="1">

                            </div>
                            <div class="form-control flex-grow ">
                                <label class="label">
                                    <span class="label-text">Adult:</span>
                                </label>
                                <input class="my-input input-sm flex-grow" placeholder="" value="2">

                            </div>

                        </div>
                        <div class="flex justify-between gap-4">

                            <div class="form-control flex-grow ">
                                <label class="label">
                                    <span class="label-text">Child(3-17):</span>
                                </label>
                                <input class="my-input input-sm flex-grow" placeholder="" value="1">

                            </div>
                            <div class="form-control flex-grow ">
                                <label class="label">
                                    <span class="label-text">Infant(0-2):</span>
                                </label>
                                <input class="my-input input-sm flex-grow" placeholder="" value="1">

                            </div>
                        </div>
                        <div class="flex justify-between gap-4">

                            <div class="form-control flex-grow ">
                                <label class="label">
                                    <span class="label-text">Luggage</span>
                                </label>
                                <input class="my-input input-sm flex-grow" placeholder="" value="1">

                            </div>

                        </div>

                    </div>

                </div>
            </x-slot>
        </x-ez-card>

    @endif


    <div class="grid grid-cols-3 gap-4">
        <div class="col-span-2 ">
            @if($step === 1)

            <x-ez-card class="">
                <x-slot name="title">Transfers</x-slot>
                <x-slot name="body">

                    <div class="card  bg-gray-200 ">
                        <div class="card-body p-2">

                            <div class="flex gap-4">
                                <div class="basis-1/5">
                                    <img class="h-24 w-full object-cover rounded-xl"
                                         src="https://api.lorem.space/image/car?hash=88560"/>
                                </div>
                                <div class="basis-4/5">
                                    <h2 class="card-title mb-2">Private transfer - Van - Valamar official</h2>
                                    <div class="flex gap-4 mb-2">
                                        <span class=" ">Type: Van</span>
                                        <span class=" ">Max. Occ: 8</span>
                                        <span class=" ">Max. Luggage: 8</span>

                                    </div>
                                    <span class="  ">Price: <b>342.25 EUR</b></span>
                                    <button class="btn btn-sm btn-primary absolute bottom-2 rounded-xl right-2" wire:click="selectTransfer">Select</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card  bg-gray-200 ">
                        <div class="card-body p-2">

                            <div class="flex gap-4">
                                <div class="basis-1/5">
                                    <img class="h-24 w-full object-cover rounded-xl"
                                         src="https://api.lorem.space/image/car?hash=82560"/>
                                </div>
                                <div class="basis-4/5">
                                    <h2 class="card-title mb-2">Private transfer - Car - Valamar official</h2>
                                    <div class="flex gap-4 mb-2">
                                        <span class=" ">Type: Van</span>
                                        <span class=" ">Max. Occ: 8</span>
                                        <span class=" ">Max. Luggage: 8</span>

                                    </div>
                                    <span class="  ">Price: <b>342.25 EUR</b></span>
                                    <button class="btn btn-sm btn-primary absolute bottom-2 rounded-xl right-2" wire:click="selectTransfer">Select</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </x-slot>
            </x-ez-card>
            @endif


            @if($step === 2)
                <div >
                    <x-ez-card  class="mb-4">
                        <x-slot name="title">Transfer details</x-slot>
                        <x-slot name="body">
                            <div class="grid grid-cols-3 gap-4">

                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Arrival flight number" value="31782563"></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Arrival date" value="01.01.2022"></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm value="12:30" label="Time of arrival" ></x-form.ez-text-input>

                                </div>

                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Departure flight number" value="1872351"></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Departure date" value="07.01.2022"></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm value="14:30" label="Time of departure" ></x-form.ez-text-input>

                                </div>

                                <div class="col-span-1">
                                    <x-form.ez-text-input sm value="14:30" label="Pickup time" ></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Child seats" value="2"></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Remark"></x-form.ez-text-input>
                                </div>
                            </div>

                        </x-slot>
                    </x-ez-card>
                    <x-ez-card class="mb-4"  >
                        <x-slot name="title">Lead traveller details</x-slot>
                        <x-slot name="body">
                            <div class="grid grid-cols-3 gap-4">

                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Title" value="Mr."></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="First name" value="John "></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm  label="Last name" value="Doe"></x-form.ez-text-input>

                                </div>

                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Reservation number" value="1872351"></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm label="Email" value="john@doe.test"></x-form.ez-text-input>
                                </div>
                                <div class="col-span-1">
                                    <x-form.ez-text-input sm value="Phone" label="+385 91 119 9111" ></x-form.ez-text-input>

                                </div>


                            </div>

                        </x-slot>
                    </x-ez-card>
                    <x-ez-card   >
                        <x-slot name="title">Other traveller details</x-slot>
                        <x-slot name="body">
                            <div class="grid grid-cols-4 gap-4">
                                @foreach($travellers as $traveler)
                                    <div class="col-span-1">
                                        <x-form.ez-text-input sm label="Title" value="Mr."></x-form.ez-text-input>
                                    </div>
                                    <div class="col-span-1">
                                        <x-form.ez-text-input sm label="First name" value="John "></x-form.ez-text-input>
                                    </div>
                                    <div class="col-span-1">
                                        <x-form.ez-text-input sm  label="Last name" value="Doe"></x-form.ez-text-input>
                                    </div>

                                    <div class="col-span-1">
                                        <x-form.ez-text-input sm label="Comment" value=""></x-form.ez-text-input>
                                    </div>

                                @endforeach

                            </div>
                            <button class="btn btn-outline ml-auto btn-sm btn-circle" wire:click="addTraveller"><i class="fas fa-plus"></i></button>

                        </x-slot>
                    </x-ez-card>
                </div>

            @endif
        </div>
        <div class="col-span-1 ">
            <x-ez-card class="sticky"  style="top:5vh">
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
