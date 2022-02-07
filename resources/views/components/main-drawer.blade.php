<div class="drawer-side  lg:p-2"><label for="main-menu" class="drawer-overlay z-10"></label>
    <aside
        class="flex flex-col  border mb-2 mt-2 md:mt-0  shadow-xl rounded-box bg-base-100 text-base-content w-80">
        <div
            class="sticky inset-x-0 bg-transparent top-0 z-50  w-full p-0 transition duration-200 ease-in-out border-b block border-base-300 ">
            <a href="{{route('profile.show')}}">
                <div class="mx-auto space-x-6 p-0  navbar max-w-none flex group hover:bg-base-200 rounded-t-2xl">
                    <div class="avatar">
                    </div>
                    <div class="block">
                        <h4 class="text-secondary font-bold subpixel-antialiased">{{Auth::user()->name}}</h4>
                        <p class="font-thin   block group-hover:hidden text-xs text-gray-400 ">My Company</p>
                        <p class="font-thin   group-hover:block hidden text-xs text-secondary ">Edit Profile</p>

                    </div>
                </div>
            </a>
        </div>

        <div>
            <ul class="menu flex flex-col  pt-4 ">
                <x-nav-link :href=" route('dashboard')" :active="request()->routeIs('dashboard')">
                    <x-slot name="icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </x-slot>
                    Pregled
                </x-nav-link>


                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full absolute bottom-4 left-0 ml-auto mr-auto btn-ghost hover:btn-error rounded-none   btn-sm">
                              Odjavi se
                            </button>
                        </form>


            </ul>
        </div>
    </aside>
</div>
