<div class=" drawer drawer-end h-0 w-0 absolute">
    <input id="right-drawer-4" type="checkbox" class="drawer-toggle ">

    <div class="drawer-side overflow-hidden  fixed  top-20 bottom-0 right-0 py-2 pointer-events-none ">
        <label for="right-drawer-4" class="drawer-overlay rounded-xl"></label>
        <ul class="menu p-4  w-80 bg-base-100  text-base-content border rounded-xl rounded-br-none rounded-tr-none border-primary pointer-events-auto">
            <li><a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full ">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </a></li>
            @if(Auth::user()->hasRole('super-admin'))
                <x-nav-link :href=" route('laravel-logs')" :active="request()->routeIs('log-view')">
                    Log View
                </x-nav-link>
                <x-nav-link :href=" route('super-admin-dashboard')"
                            :active="request()->routeIs('super-admin-dashboard')">
                    Super Admin Dashboard
                </x-nav-link>
            @endif
        </ul>
    </div>
</div>
