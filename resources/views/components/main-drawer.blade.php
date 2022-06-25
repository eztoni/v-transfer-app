<div class="drawer-side main-drawer  lg:p-2"><label for="main-menu" class="drawer-overlay z-10"></label>

    <aside
        style="max-width: 260px;"
        class="flex flex-col  relative border mb-2 mt-2 md:mt-0  shadow-xl rounded-box bg-base-100 text-base-content w-80">
        <img class="logo-img absolute" src="{{asset('/storage/static_images/valamar-puzina.png')}}">

        <div
            class="sticky inset-x-0 bg-transparent top-0 z-50  w-full p-0 transition duration-200 ease-in-out border-b block border-base-300 ">
            <a href="{{route('profile.show')}}">
                <div class="mx-auto space-x-6 p-0  navbar max-w-none flex group hover:bg-base-200 rounded-t-2xl">
                    <div class="avatar">
                    </div>
                    <div class="block">
                        <h4 class="text-primary font-bold subpixel-antialiased">{{Auth::user()->name}}</h4>
                        <p class="font-thin   block group-hover:hidden text-xs text-gray-400 ">My Company</p>
                        <p class="font-thin   group-hover:block hidden text-xs text-secondary ">Edit Profile</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="border-b border-base-300 p-4 py-1 ">
            <div class="flex justify-between">
                <a class="btn btn-circle  btn-ghost "
                   @if(\Auth::user()->hasAnyRole(App\Models\User::ROLE_SUPER_ADMIN

                    ,App\Models\User::ROLE_ADMIN))
                   href="{{route('admin.company-dashboard')}}"
                    @endif
                >
                    <i class="fas fa-building text-xl "></i>
                </a>
                <button class="btn btn-circle  btn-ghost">
                    <i class="fas fa-users text-xl"></i>
                </button>
                <button class="btn btn-circle  btn-ghost">
                    <i class="fas fa-truck-loading"></i>
                </button>
            </div>
        </div>

        <div>
            <ul class="menu flex flex-col  pt-4 ">
                @foreach($menuItems as $item)
                    @if(!array_key_exists('items',$item) && $item['show'])
                        <x-nav-link :href="$item['href']" :active="$item['active']">
                            <x-slot name="icon">
                                <i class="{{$item['icon']}}"></i>
                            </x-slot>
                            {{$item['text']}}
                        </x-nav-link>
                    @elseif($item['show'])
                        @php
                            $subActive = !empty(Arr::where($item['items'],function ($value,$key){
                                return $value['active'];
                            }));

                        @endphp
                        <x-nav-submenu :active="$subActive"  :text="$item['text']" :items="$item['items']">
                                <i class="{{$item['icon']}}"></i>
                        </x-nav-submenu>
                    @endif
                @endforeach
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full absolute bottom-4 left-0 ml-auto mr-auto btn-ghost hover:btn-error rounded-none   btn-sm">
                        Odjavi se
                    </button>
                </form>


            </ul>
        </div>
    </aside>
</div>
