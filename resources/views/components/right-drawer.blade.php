<div class=" ds-drawer ds-drawer-end h-0 w-0 absolute">
    <input id="right-drawer-4" type="checkbox" class="ds-drawer-toggle ">

    <div class="ds-drawer-side overflow-hidden  fixed  top-20 bottom-0 right-0 py-2 pointer-events-none ">
        <label for="right-drawer-4" class="ds-drawer-overlay rounded-xl"></label>
        <ul class="ds-menu p-4  w-80 bg-base-100  text-base-content border rounded-xl rounded-br-none rounded-tr-none pointer-events-auto">
            <h2 class="font-bold text-red-600">Super Admin Menu</h2>
            <hr class="opacity-50 my-2">

            @if(Auth::user()->hasRole('super-admin'))
                <x-nav-link :href="route('laravel-logs')" :active="request()->routeIs('log-view')">
                    <x-slot name="icon">
                        <i class="fas fa-warehouse"></i>
                    </x-slot>
                    Error Log View
                </x-nav-link>
                <x-nav-link :href="route('company-overview')" :active="request()->routeIs('company-overview')">
                    <x-slot name="icon">
                        <i class="fas fa-archway"></i>
                    </x-slot>
                    Company Overview
                </x-nav-link>
                <x-nav-link :href="route('language-overview')" :active="request()->routeIs('language-overview')">
                    <x-slot name="icon">
                        <i class="fas fa-language"></i>
                    </x-slot>
                    Languages Overview
                </x-nav-link>
                <x-nav-link :href="route('super-admin-dashboard')"
                            :active="request()->routeIs('super-admin-dashboard')">
                    <x-slot name="icon">
                        <i class="fas fa-user-cog"></i>
                    </x-slot>
                    Super Admin Dashboard
                </x-nav-link>
                <x-nav-link :href="route('activity-log-dashboard')"
                            :active="request()->routeIs('activity-log-dashboard')">
                    <x-slot name="icon">
                        <i class="far fa-address-card"></i>
                    </x-slot>
                    Activity Log
                </x-nav-link>
                <x-nav-link :href="route('phpinfo')" :active="request()->routeIs('phpinfo')">
                    <x-slot name="icon">
                        <i class="far fa-address-card"></i>
                    </x-slot>
                    PHP info
                </x-nav-link>

                <x-nav-link href="#" x-data="{}"
                            @click="localStorage.removeItem('hide-res-dev-tools');window.location.reload()">
                    <x-slot name="icon">
                        <i class="fas fa-database"></i>
                    </x-slot>
                    Enable Internal Dev tools
                </x-nav-link>

                <x-nav-link href="{{route('owner-overview')}}" class="flex">
                    <div class="">
                        <x-slot name="icon">

                            <x-icon name="library" class="w-4 h-4"></x-icon>
                        </x-slot>

                        Owner Overview
                    </div>

                </x-nav-link>

                @if( \Illuminate\Support\Facades\Auth::user()->hasRole('super-admin'))
                    <x-nav-link href="{{route('dev-mail-preview')}}">
                        <x-slot name="icon">
                            <i class="fas fa-envelope"></i>
                        </x-slot>
                        Mail preview
                    </x-nav-link>
                @endif
            @endif
        </ul>
    </div>
</div>
