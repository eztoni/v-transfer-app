<div class="ds-navbar border shadow-sky-100 mb-2 shadow-md rounded-lg bg-base-100 " >
    <div class="flex-none">
        <label for="main-menu" class="ds-btn ds-btn-square ds-btn-ghost ds-drawer-button lg:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 class="inline-block w-6 h-6 stroke-current">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </label></div>
    <div class="flex-1 px-2 mx-2">

        <img width="175" src="{{asset('storage/static_images/valamar_horizontal.png')}}">
    </div>
    @role(\App\Models\User::ROLE_SUPER_ADMIN)
    <div class="flex-none hidden px-2 mx-2 lg:flex">
        <livewire:company-switcher></livewire:company-switcher>
    </div>
    @endrole

    @hasanyrole(\App\Models\User::ROLE_SUPER_ADMIN.'|'.\App\Models\User::ROLE_ADMIN)
    <div class="flex-none hidden px-2 mx-2 lg:flex">
        <livewire:owner-switcher></livewire:owner-switcher>
    </div>
    @endhasanyrole

    @hasanyrole(\App\Models\User::ROLE_SUPER_ADMIN.'|'.\App\Models\User::ROLE_ADMIN.'|'.\App\Models\User::ROLE_USER)
    <div class="flex-none hidden px-2 mx-2 lg:flex">
        <livewire:destination-switcher></livewire:destination-switcher>
    </div>
    @endhasanyrole
    @if(Auth::user()->hasRole('super-admin'))
        <div class="flex-none">
            <label class="ds-btn ds-btn-square ds-btn-ghost" for="right-drawer-4" >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-6 h-6 stroke-current">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </label>
        </div>
    @endif
</div>



