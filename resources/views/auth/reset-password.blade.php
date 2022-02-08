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
                    <img src="{{URL('/img/logo.png')}}">
                </div>
            <!-- <p class="pt-3">Not a member?
                    <b><a
                            class=" underline text-primary  hover:text-gray-900"
                            href="{{ route('register') }}">
                            {{ __('Sing Up Now') }}
                </a></b>
        </p> -->
            </div>

            <div class="flex flex-col items-center w-full mt-18  md:my-60">

                <h1 class="font-bold text-4xl text-center  my-8">Password Reset <span class="text-primary">valamar</span>
                </h1>

                <form method="POST" action="{{ route('password.update') }}" class="w-9/12 max-w-screen-sm">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <x-jet-label for="email" value="{{ __('Email') }}"/>
                        <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email"
                                     :value="old('email', $request->email)" required autofocus/>
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="password" value="{{ __('Password') }}" />
                        <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                        <x-jet-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                    </div>

                    <x-jet-validation-errors class="mb-4"/>

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="flex items-center justify-between mt-4">
                        @if (Route::has('login'))
                            <a class="underline text-sm text-gray-600 hover:text-gray-900"
                               href="{{ route('login') }}">
                                {{ __('Login') }}
                            </a>
                        @endif


                        <button type="submit" class="btn flex-shrink-0 btn-primary hove:shadow-xl">
                            {{ __('Restart') }}

                        </button>

                    </div>
                </form>

            </div>


        </div>

    </div>
</x-guest-layout>
