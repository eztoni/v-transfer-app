<div>
    <x-ez-card>

        <x-slot name="title">
            Search
        </x-slot>

        <x-slot name="body" class="">
            <div class="flex justify-between gap-4">
                <div class="form-control flex-grow basis-1/3">
                    <select class="my-select" wire:model="destinationId">
                        <option value="">Pick a destination</option>
                        @foreach(\App\Models\Destination::all() as $destination)
                            <option value="{{$destination->id}}">{{$destination->name}}</option>

                        @endforeach


                    </select>

                </div>

                <div class="form-control flex-grow basis-1/3">
                    @if(!empty($this->destinationId))
                        <select class="my-select" wire:model="pickupPointId">
                            <option value="">Pickup location</option>

                        </select>
                    @endif


                </div>
                <div class="form-control flex-grow basis-1/3">
                    @if(!empty($this->destinationId))

                        <select class="my-select" wire:model="dropOffPointId">
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
                        <label class="label cursor-pointer bg-gray-100 rounded my-4  ">
                            <span class="label-text">Two way</span>
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
            <button class="ml-auto btn btn-primary btn-sm mt-4">Search</button>
        </x-slot>
    </x-ez-card>
</div>
