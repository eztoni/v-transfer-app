<div x-data="data()" class="grid grid-cols-1   md:grid-cols-6 gap-4  rounded-box">

    <div class="col-span-6  grid-cols-1   md:grid-cols-6 gap-4 grid">
        <div class="md:col-span-2 col-span-6">
            <x-bg-text> Service Rate Plans:
                <x-slot name="description">
                    A rate plan describes the combination of different factors that make up a booking.
                    These include payment and cancellation policies, prices, number of days between booking and
                    check-in, service length etc.
                    You can imagine rate plans like a tour options.
                    <br>
                    <br>
                    <span class="font-semibold">You can create infinite number of rate plans, each rate plan can serve it's purpose.
                        You can define public rate plans to be available on your website and you can define some of the rate plans to be available for your other booking channles like B2B. </span>
                </x-slot>
            </x-bg-text>
        </div>
        <div class="md:col-span-4 col-span-6">
            @foreach($ratePlans as  $rp)
                <x-ez-card class="overflow-visible mb-5">
                    <x-slot name="title" class="flex justify-between">
                        <span>Rate Plan: {{($loop->index+1)}}</span>
                        <button wire:click="removeRp({{$loop->index}})" class="btn btn-warning btn-sm">Remove</button>
                    </x-slot>

                    <x-slot name="body">
                            <div class="divider opacity-25"></div>
                        <div class="flex flex-col md:flex-row gap-4">
                            <div><label class="label">
                                    <span class="label-text">Rate Plan Image:</span>
                                </label>
                                <figure><img class="rounded-box w-48 h-52"
                                             src="https://phantom-marca.unidadeditorial.es/98164569f095e5ab4d7004c149236644/resize/1320/f/jpg/assets/multimedia/imagenes/2021/06/30/16250618528957.jpg">
                                </figure>
                            </div>
                            <div class="flex-grow">
                                <div class="form-control ">
                                    <label class="label">
                                        <span class="label-text">Service Image:</span>
                                    </label>
                                    <input class="input input-bordered " wire:model="ratePlans.{{$loop->index}}.name" placeholder="Service Name">
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Service Description</span>
                                    </label>

                                    <textarea class="textarea h-32  textarea-bordered "
                                              placeholder="Sevice Description"></textarea>
                                </div>
                            </div>
                        </div>

                        <hr class="my-6">
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="form-control flex-1">
                                <label class="label">
                                    <span class="label-text">Rate Plan pricing type:</span>
                                </label>
                                <select class="select select-bordered">
                                    <option>Per Service</option>
                                    <option>Per Pax</option>
                                    <option>Per Person</option>
                                </select>
                            </div>
                            <div class="form-control flex-1">
                                <label class="label">
                                    <span class="label-text">Rate plan sale channel:</span>
                                </label>
                                <select class="select select-bordered">
                                    <option>Public</option>
                                    <option>Private</option>
                                </select>
                            </div>


                            <div class="form-control flex-1 ">
                                <label class="label">
                                    <span class="label-text">Inventory</span>
                                </label>
                                <select class="select select-bordered">
                                    <option>Boat- Betula 3772 Inventory</option>
                                    <option>Markan</option>
                                </select>
                            </div>
                        </div>
                        <hr class="my-6">
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="form-control flex-1">
                                <label class="label">
                                    <span class="label-text">Release period:</span>
                                </label>
                                <input class="input input-bordered " placeholder="Release period">
                            </div>


                            <div class="form-control flex-1 ">
                                <label class="label">
                                    <span class="label-text">Release period metrics:</span>
                                </label>
                                <select class="select select-bordered">
                                    <option>Minutes</option>
                                    <option selected>Hours</option>
                                    <option>Days</option>
                                </select>
                            </div>
                            <div class="form-control flex-1">
                                <label class="label">
                                    <span class="label-text">Book in advance limit:</span>
                                </label>
                                <input class="input input-bordered " placeholder="Book in advance">
                                <small class="opacity-75 text-xs pl-1">Book in advance metrics: Days</small>
                            </div>
                        </div>
                        <hr class="my-6">
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="form-control flex-1">
                                <label class="label">
                                    <span class="label-text">Service Times</span>
                                </label>
                                @php
                                    $testArray= ['1'=>'18:00',
                                         '2' => '19:00',
                                         '3' => '19:30',
                                         '4' => '19:45',
                                         '5' => '20:00',
                                         '6' => '20:30',
                                         '7' => '21:00',
                                         '8' => '21:30',
                                         '9' => '21:40',
                                         '10' => '21:50',
                                         '11' => '22:20',
                                         '12' => '22:30',
                                         ];
                                @endphp
                                <x-ez-multi-select wrap class="max-w-md" name="rp-time[37][]"
                                                   :itemsKeyValue="$testArray"><p class="opacity-75">Pick RP times</p>
                                </x-ez-multi-select>
                            </div>


                            <div class="form-control flex-1 ">
                                <label class="label">
                                    <span class="label-text">Release period metrics:</span>
                                </label>
                                <select class="select select-bordered" x-init="">
                                    <option>Minutes</option>
                                    <option selected>Hours</option>
                                    <option>Days</option>
                                </select>
                            </div>
                            <div class="form-control flex-1">
                                <label class="label">
                                    <span class="label-text">Book in advance limit:</span>
                                </label>
                                <input class="input input-bordered " placeholder="Book in advance">
                                <small class="opacity-75 text-xs pl-1">Book in advance metrics: Days</small>
                            </div>
                        </div>

                    </x-slot>
                </x-ez-card>
            @endforeach
            <button class="w-full btn btn-outline my-5" wire:click="addRp">
                <x-icons.plus></x-icons.plus>
                Add Rate Plan
            </button>
        </div>

    </div>

    <hr class="col-span-6">
    <div class="md:col-span-2 col-span-6">
        <x-bg-text>
            Save the Rate plans:
            <x-slot name="description">
                When you proceed to the next step, these rate plans will be saved under your services
            </x-slot>
        </x-bg-text>
    </div>
    <div class="  md:col-span-4 col-span-6  mb-32">
        <x-ez-card>
            <x-slot name="body" class="flex flex-row justify-end p-4 ">

                <button class="btn btn-success hover:shadow-xl hover:scale-105 " wire:click="nextStep">Save Rate plans
                    and proceed!
                    <span
                        class="pl-2 scale-75 ">@include('components.icons.arrow-right')</span></button>
            </x-slot>
            (
        </x-ez-card>
    </div>

</div>

<script>

    /*
    If i type $ in console, everything works
     */


</script>
