<div x-data="reservationSettings">
    <x-ez-card class="mb-4">

        <x-slot name="title">
            <div class="flex flex-col">
                <div class="text-sm breadcrumbs">
                    <ul>
                        <li><a href="{{route('bookings')}}">Bookings</a></li>
                        <li class="text-info">RES #584372032</li>
                    </ul>
                </div>
                <div>
                    <p class="title font-extrabold">RES #584372032</p>
                </div>
            </div>
        </x-slot>

        <x-slot name="body">
        </x-slot>
    </x-ez-card>

    <div class="tabs mb-2">
        <a class="tab tab-lifted" :class="{ 'tab-active': tab === 'reservation' }" x-on:click.prevent="tab = 'reservation'" href="#">Reservation</a>
        <a class="tab tab-lifted" :class="{ 'tab-active': tab === 'invoice' }" x-on:click.prevent="tab = 'invoice'" href="#">Invoices</a>
        <a class="tab tab-lifted" :class="{ 'tab-active': tab === 'payments' }" x-on:click.prevent="tab = 'payments'" href="#">Payments</a>
        <a class="tab tab-lifted" :class="{ 'tab-active': tab === 'log' }" x-on:click.prevent="tab = 'log'" href="#">Log</a>
    </div>

    <!-- TAB RESERVATION -->
    <div x-show="tab === 'reservation'" class="grid md:grid-cols-3 grid-cols-1 gap-4">

        <div class="">
            <x-ez-card class="mb-4">

                <x-slot name="title" class="mb-0 flex justify-between">
                    <div>
                        <span class="text-md">Transfer</span>
                    </div>
                    <a href="{{route('reservation-view')}}"><button class="btn btn-sm btn-primary">View</button></a>
                </x-slot>

                <x-slot name="body">
                    <div class="divider mt-0 mb-0"></div>

                    <div class="flex flex-col w-full">
                        <span>Seller :  <span class="text-info">Valamar Rivijera</span> </span>
                        <span>Type :  <span class="badge badge-success">One Way</span> </span>
                        <span>Total :  <b>EUR 257</b> </span>
                        <span>Status :  <span class="badge badge-success">Confirmed</span> </span>
                        <span>Payment Status :  <span class="badge badge-success">Paid in full</span> </span>
                        <span>Created :  <span>Tue 26. Apr 2022 12:45:34</span> </span>
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
                        <span>Name :  <span class="text-info font-bold">Mr. Joe Doe</span> </span>
                        <span>Email :  <a href="mailto: joeboy@jondoe.com"> joeboy@jondoe.com</a></span>
                        <span>Phone :  <a href="tel:123-456-7890">123-456-7890</a> </span>
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
                            <span>Passangers :  <b>4</b> </span>
                            <span>Luggage :  <b>4</b> </span>
                            <span>Pickup :  <b>Zadar - <b>25.5.2022</b> @ <b>12:45</b></b>  </span>
                            <span>Dropoff :  <b>Šibenik - <b>28.5.2022</b> @ <b>12:45</b></b>  </span>
                        </div>

                        <div class="flex flex-grow flex-col gap-2">
                            <button class="btn btn-sm btn-primary"><i class="fas fa-download mr-2"></i> Ticket</button>
                            <button class="btn btn-sm btn-warning"><i class="fas fa-times mr-2"></i> Cancel Booking</button>
                        </div>
                    </div>

                    <div class="divider mt-0 mb-0"></div>

                    <p class="text-xl font-extrabold">Other Travellers</p>
                    <div class="flex flex-wrap md:flex-col gap-4 flex-col">
                        <div class="flex flex-wrap md:flex-row flex-col">
                            <p class="text-info">Passanger #1:</p>
                            <p>Title: Mrs.</p>
                            <p>First Name: Smith</p>
                            <p>Last Name: Jones</p>
                            <p>Comment: Will pay for the trip</p>
                            <button class="btn md:btn-circle btn-sm btn-success">
                                <i class="fas fa-pen"></i>
                            </button>
                        </div>

                        <div class="flex flex-wrap md:flex-row flex-col">
                            <p class="text-info">Passanger #2:</p>
                            <p>Title: - </p>
                            <p>First Name: - </p>
                            <p>Last Name: - </p>
                            <p>Comment: - </p>
                            <button class="btn md:btn-circle btn-sm btn-success">
                                <i class="fas fa-pen"></i>
                            </button>
                        </div>
                        <div class="flex flex-wrap md:flex-row flex-col">
                            <p class="text-info">Passanger #3:</p>
                            <p>Title: - </p>
                            <p>First Name: - </p>
                            <p>Last Name: - </p>
                            <p>Comment: - </p>
                            <button class="btn md:btn-circle btn-sm btn-success">
                                <i class="fas fa-pen"></i>
                            </button>
                        </div>
                    </div>



                </x-slot>
            </x-ez-card>
        </div>
    </div>
    <!-- END OF TAB RESERVATION -->

    <!-- TAB INVOICE -->
    <div x-show="tab === 'invoice'" class="grid grid-cols-3 gap-4">

       Invoices

    </div>
    <!-- END OF TAB INVOICE -->


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
