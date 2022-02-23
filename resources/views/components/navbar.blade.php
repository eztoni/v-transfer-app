<div class="navbar border  mb-4 shadow-lg rounded-box bg-base-100 " >
    <div class="flex-none"><label for="main-menu" class="btn btn-square btn-ghost drawer-button lg:hidden">
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

    @if(Auth::user()->hasRole('super-admin'))
        <div class="flex-none">
            <label class="btn btn-square btn-ghost" for="right-drawer-4" >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-6 h-6 stroke-current">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </label>
        </div>
    @endif
</div>

