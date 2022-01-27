<div x-data="{}" class="grid grid-cols-1   md:grid-cols-6 gap-4  rounded-box">

<div class="col-span-6  grid-cols-1   md:grid-cols-6 gap-4 grid">
        <div class="md:col-span-2 col-span-6">
            <x-bg-text> Basic service data:
                <x-slot name="description">
                    Ensure your service name is descriptive, simple and catchy to create more conversions!
                </x-slot>
            </x-bg-text>
        </div>
        <div class="md:col-span-4 col-span-6">
            <x-ez-card>
                <x-slot name="body">
                    <label class="label">
                        <span class="label-text">Service Name:</span>
                    </label>
                    <input class="input input-bordered" placeholder="Service Name">
                    <label class="label">
                        <span class="label-text">Service Category</span>
                    </label>
                    <select class="select select-bordered w-full ">
                        <option disabled="disabled" selected="selected">Choose your service category!</option>
                        <option>Adventure tours</option>
                        <option>Buggy tours</option>
                        <option>Boat tour</option>
                    </select>
                    <label class="label">
                        <span class="label-text">Service Supplier</span>
                    </label>
                    <select class="select select-bordered w-full ">
                        <option disabled="disabled" selected="selected">Choose your service supplier!</option>
                        <option>My agency</option>
                        <option>Zipline tours</option>
                        <option>Adriana</option>
                    </select>

                </x-slot>
            </x-ez-card>
        </div>
        <hr class="col-span-6 ">
        <div class="md:col-span-2 col-span-6">
            <x-bg-text> Service Images:
                <x-slot name="description">
                    Please upload a nice quality images that describe your tour. Max image size is 2 megabytes and
                    allowed image types are: jpg, jpeg, png, webp.
                </x-slot>
            </x-bg-text>
        </div>
        <div class="md:col-span-4 col-span-6">
            <x-ez-card>
                <x-slot name="body">
                    <div class="carousel rounded-box">
                        <div class="carousel-item">
                            <img src="https://picsum.photos/id/500/256/144">
                        </div>
                        <div class="carousel-item">
                            <img src="https://picsum.photos/id/501/256/144">
                        </div>
                        <div class="carousel-item">
                            <img src="https://picsum.photos/id/502/256/144">
                        </div>
                        <div class="carousel-item">
                            <img src="https://picsum.photos/id/503/256/144">
                        </div>
                        <div class="carousel-item">
                            <img src="https://picsum.photos/id/504/256/144">
                        </div>
                        <div class="carousel-item">
                            <img src="https://picsum.photos/id/505/256/144">
                        </div>
                        <div class="carousel-item">
                            <img src="https://picsum.photos/id/506/256/144">
                        </div>
                    </div>

                </x-slot>
            </x-ez-card>
        </div>
        <hr class="col-span-6 ">
        <div class="md:col-span-2 col-span-6 ">
            <x-bg-text> Service Descriptions:
                <x-slot name="description">
                    Describe your service in the best way you can!
                    Use our editors to highlight important service data.
                </x-slot>
            </x-bg-text>
        </div>
        <div class="md:col-span-4 col-span-6">
            <x-ez-card>
                <x-slot name="body">
                    <label class="label">
                        <span class="label-text">Service Youtube Video Link</span>
                    </label>
                    <input class="input input-bordered" placeholder="Service Video">
                    <label class="label">
                        <span class="label-text">Service Description</span>
                    </label>

                    <textarea class="textarea h-32 textarea-bordered textarea-bordered"
                              placeholder="Sevice Description"></textarea>

                </x-slot>
            </x-ez-card>
        </div>
        <hr class="col-span-6">
        <div class="md:col-span-2 col-span-6 ">
            <x-bg-text> Service Age Groups:
                <x-slot name="description">
                    In EZ Booker it's possible to add your own custom age groups, so your agency can make price
                    differentiation based on guest's age.
                </x-slot>
            </x-bg-text>
        </div>
        <div class="md:col-span-4 col-span-6">
            <x-ez-card>
                <x-slot name="body">
                    <div class="flex md:flex-row gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span cla="label-text">Age Category</span>
                            </label>
                            <select class="select select-bordered  w-full max-w-xs select-disabled" disabled placeholder="Adult" value="Adult">
                                <option selected>Adult</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Age From:</span>
                            </label>
                            <input class="input input-bordered w-full max-w-xs "  placeholder="18" value="18">

                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Age To:</span>
                            </label>
                            <input class="input input-bordered w-full max-w-xs "  placeholder="99" value="99">

                        </div>
                        <div class="form-control">
                        <label class="label">
                            <span class="label-text">
                                Add new age group
                            </span>
                        </label>
                        <button class="btn btn-square btn-sm md:btn-md btn-success  ">
                           @include('components.icons.plus')
                        </button>
                        </div>
                    </div>



                </x-slot>
            </x-ez-card>
        </div>
        <hr class="col-span-6">
        <div class="md:col-span-2 col-span-6 ">
            <x-bg-text> Service Times:
                <x-slot name="description">
                    Define your services departure times so guest can choose the best time that suits for them!
                </x-slot>
            </x-bg-text>
        </div>
        <div class="md:col-span-4 col-span-6">
            <x-ez-card>
                <x-slot name="body">
                    <h4 class="mb-6">Departure times:</h4>

                    <div class="flex md:flex-row gap-4">
                        <div class="form-control ">

                            <input type="time" class="input input-bordered" value="12:00">
                        </div>

                        <div class="form-control">

                            <button class="btn btn-square btn-sm md:btn-md btn-success  ">
                                @include('components.icons.plus')
                            </button>
                        </div>
                    </div>



                </x-slot>
            </x-ez-card>
        </div>
    </div>

    <hr class="col-span-6">
    <div class="md:col-span-2 col-span-6">
        <x-bg-text>
            Save the service:
            <x-slot name="description">
               When you proceed to the next step, this service will be saved under your services.
            </x-slot>
        </x-bg-text>
    </div>
    <div class="  md:col-span-4 col-span-6  mb-32">
        <x-ez-card>
            <x-slot name="body" class="flex flex-row justify-end p-4 ">

                <button class="btn btn-success hover:shadow-xl hover:scale-105 " wire:click="nextStep" >Save service and proceed!
                    <span
                        class="pl-2 scale-75 ">@include('components.icons.arrow-right')</span></button>
            </x-slot>
            (
        </x-ez-card>
    </div>

</div>
