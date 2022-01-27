<x-app-layout>
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4  rounded-box">

        @if(Auth::user()->hasRole('super-admin'))
            <div class="col-span-6">
                <a style="float:right" href="{{route('super-admin-dashboard')}}"><button class="btn btn-sm btn-primary">Return to Admin</button></a>
            </div>
        @endif

        <div class="col-span-6  grid-cols-1   md:grid-cols-6 gap-4 grid">
            <div class="md:col-span-2 col-span-6">
                <x-bg-text> User Data:
                    <x-slot name="description">
                        User basic information
                    </x-slot>
                </x-bg-text>
            </div>
            <div class="md:col-span-4 col-span-6">
                @livewire('edit-user-data-by-superadmin', ['user' => $user])
            </div>
        </div>
    </div>
</x-app-layout>
