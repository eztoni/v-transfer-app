<div x-data="reservationSettings">
    <x-ez-card class="mb-4">

        <x-slot name="title">
            <div class="flex flex-col">
                <div class="text-sm breadcrumbs">
                    <ul>
                        <li><a href="{{route('bookings')}}">Bookings</a></li>
                        <li class="text-info">RES #{{$this->reservation->id}}</li>
                    </ul>
                </div>
                <div>
                    <p class="title font-extrabold">RES #{{$this->reservation->id}}</p>
                </div>
            </div>
        </x-slot>

        <x-slot name="body">
        </x-slot>
    </x-ez-card>

    <div class="tabs mb-2">
        {{--        <a class="tab tab-lifted" :class="{ 'tab-active': tab === 'reservation' }" x-on:click.prevent="tab = 'reservation'" href="#">Reservation</a>--}}
        {{--        <a class="tab tab-lifted" :class="{ 'tab-active': tab === 'invoice' }" x-on:click.prevent="tab = 'invoice'" href="#">Invoices</a>--}}
        {{--        <a class="tab tab-lifted" :class="{ 'tab-active': tab === 'payments' }" x-on:click.prevent="tab = 'payments'" href="#">Payments</a>--}}
        {{--        <a class="tab tab-lifted" :class="{ 'tab-active': tab === 'log' }" x-on:click.prevent="tab = 'log'" href="#">Log</a>--}}
    </div>


    <!-- TAB RESERVATION -->
    <div x-show="tab === 'reservation'" class="grid md:grid-cols-3 grid-cols-1 gap-4">

        <div class="">
            <x-ez-card class="mb-4">

                <x-slot name="title" class="mb-0 flex justify-between">
                    <div>
                        <span class="text-md">Transfer details</span>
                    </div>
                </x-slot>

                <x-slot name="body">
                    <div class="divider mt-0 mb-0"></div>
                    <table class="table table-compact w-full">

                        <tbody>
                        <tr>
                            <td class="font-bold">Transfer:</td>
                            <td>{{\Arr::first($this->reservation->transfer['name'])}}</td>

                        </tr>
                        <tr>
                            <td class="font-bold">Transfer:</td>
                            <td>{{\Arr::first($this->reservation->vehicle)}}</td>

                        </tr>


                        </tbody>
                    </table>
                    <div class="flex flex-col w-full">
                        <span>Seller :  <span class="text-info">Valamar Rivijera</span> </span>
                        <span>Type :  <span class="badge badge-success">One Way</span> </span>
                        <span>Total :  <b>{{$this->reservation->getPrice()}}</b> </span>
                        <span>Status :  <span class="badge badge-success">Confirmed</span> </span>
                        <span>Payment Status :  <span class="badge badge-success">Paid in full</span> </span>
                        <span>Created :  <span>{{$this->reservation->created_at->format('d.m.Y H:i')}}</span> </span>
                    </div>
                </x-slot>
            </x-ez-card>

            <x-ez-card class="mb-4">

                <x-slot name="title" class="mb-0 flex justify-between">
                    <div>
                        <span class="text-md">Lead Traveler</span>

                    </div>
                </x-slot>

                <x-slot name="body">
                    <div class="divider mt-0 mb-0"></div>

                    <div class="flex flex-col w-full">
                        <span>Name :  <span
                                class="text-info font-bold">{{$this->leadTraveller->full_name}}</span> </span>
                        <span>Email :  <a href="mailto: joeboy@jondoe.com">{{$this->leadTraveller->email}}</a></span>
                        <span>Phone :  <a href="tel:123-456-7890">{{$this->leadTraveller->phone}}</a> </span>
                    </div>
                </x-slot>
            </x-ez-card>
        </div>

        <div class="md:col-span-2">
            <x-ez-card class="mb-4">

                <x-slot name="title" class="mb-0 flex justify-between">
                    <div>
                        <span class="text-md">Reservation infromation</span>

                    </div>
                </x-slot>

                <x-slot name="body">
                    <div class="divider mt-0 mb-0"></div>

                    <div class="flex md:flex-row gap-4 flex-col w-full">

                        <div class="basis-2/3 flex flex-col ">
                            <span>Passangers :  <b>{{$this->reservation->num_passangers}}</b> </span>
                            <span>Luggage :  <b>{{$this->reservation->luggage}}</b> </span>
                            <span>Pickup :  <b>{{$this->pickupLocationString}}</b>  </span>
                            <span>Dropoff :  <b>Šibenik - <b>28.5.2022</b> @ <b>12:45</b></b>  </span>
                        </div>

                        <div class="flex flex-grow flex-col gap-2">
                            <button class="btn btn-sm btn-warning"><i class="fas fa-times mr-2"></i> Cancel Booking
                            </button>
                        </div>
                    </div>

                    <div class="divider mt-0 mb-0"></div>


                    @if($this->otherTravellers->isNotEmpty())
                        <p class="text-xl font-extrabold">Other Travellers</p>

                        <table class="table table-compact w-full">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Title:</th>
                                <th>First Name:</th>
                                <th>Last Name:</th>
                                <th>Comment</th>
                                <th>Edit</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($this->otherTravellers as $otherTraveller)
                                <tr>

                                    <td class="text-info">{{$loop->iteration}}:</td>
                                    <td>{{$otherTraveller->title}}</td>
                                    <td> {{$otherTraveller->first_name}}</td>
                                    <td> {{$otherTraveller->last_name}}</td>
                                    <td> {{$otherTraveller->reservations->first()->pivot->comment}}</td>
                                    <td>
                                        <button wire:click="openOtherTravellerModal({{$otherTraveller->id}})"
                                                class="btn md:btn-circle btn-sm btn-success">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>



                    @endif


                </x-slot>
            </x-ez-card>
        </div>
    </div>
    <!-- END OF TAB RESERVATION -->

    <!-- TAB INVOICE -->
{{--    <div x-show="tab === 'invoice'" class="grid grid-cols-3 gap-4">--}}

{{--       Invoices--}}

{{--    </div>--}}
<!-- END OF TAB INVOICE -->


    <div class="modal {{ $otherTravellerModal ? 'modal-open fadeIn' : '' }}">
        <div class="modal-box max-h-screen overflow-y-auto">
            Update other traveller data
            <hr class="my-4">

            <div class="form-control">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Title :</span>
                    </label>
                    <input wire:model="otherTraveller.title" class="input input-bordered"
                           placeholder="Title">
                    @error('otherTraveller.title')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>
            </div>

            <div class="form-control">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">First Name :</span>
                    </label>
                    <input wire:model="otherTraveller.first_name" class="input input-bordered"
                           placeholder="First Name">
                    @error('otherTraveller.first_name')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>
            </div>

            <div class="form-control">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Last Name :</span>
                    </label>
                    <input wire:model="otherTraveller.last_name" class="input input-bordered"
                           placeholder="Last Name">
                    @error('otherTraveller.last_name')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>
            </div>

            <div class="form-control">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Comment :</span>
                    </label>
                    <input wire:model="otherTravellerComment" class="input input-bordered"
                           placeholder="Comment">
                    @error('otherTravellerComment')
                    <x-input-alert type='warning'>{{$message}}</x-input-alert>
                    @enderror
                </div>
            </div>


            <div class="mt-4 flex justify-between">
                <button wire:click="closeOtherTravellerModal()" class="btn btn-sm ">Close</button>
                <button wire:click="saveOtherTravellerData()"
                        class="btn btn-sm ">Update
                </button>
            </div>
        </div>
    </div>


    <script>
        function reservationSettings() {
            return {
                tab: 'reservation',
                init() {

                }
            }
        }
    </script>

</div>
