<x-guest-layout>


    <div class="min-h-screen grid grid-cols-3 items-center bg-base-200">

        <!-- GRID #1 -->
        <div
            class=" lg:col-span-1 lg:block hidden "
            style="height:100vh; animation: fade-in 0.75s cubic-bezier(0.39,0.575,0.565,1) forwards;
                background-size: cover;
                background-position: bottom;
                background-repeat: no-repeat;
                background-image: url('{{URL('/img/bg_image.jpg')}}');
                background-image: linear-gradient(
                180deg,hsla(0,0%,100%,0) 42.52%,#0d3151),"
        >
        </div>

        <!-- GRID #2 -->
        <div class=" lg:col-span-2 col-span-3 h-screen lg:px-20 px-5">
            <!-- Nav bar -->
            <div class="flex sm:justify-between flex-col  items-center sm:flex-row gap-5  py-8 mb-auto">
                <div class="w-40 flex-shrink-0">
                    <img src="https://www.ez-booker.com/wp-content/uploads/2019/06/ez-booker-logo-color.svg">
                </div>
                <form class="pt-3" method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="underline text-primary  hover:text-gray-900">
                        <b>{{ __('Log Out') }}</b>
                    </button>
                </form>
            </div>

            <div class="flex flex-col items-center w-full mt-18  md:my-48">

                <h1 class="font-bold text-4xl text-center  my-8">Verify Email for <span class="text-primary">EZ Booker</span>
                </h1>

                <div class="mb-4 text-sm text-center text-gray-600">
                    {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?.') }}
                    <br>
                    <b>{{ __('If you didn\'t receive the email, we will gladly send you another.') }}</b>
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                    </div>
                @endif

                <div class="mt-4 flex items-center justify-between">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf

                        <div>
                            <x-jet-button type="submit">
                                {{ __('Resend Verification Email') }}
                            </x-jet-button>
                        </div>
                    </form>
                </div>


            </div>


        </div>

    </div>
</x-guest-layout>
