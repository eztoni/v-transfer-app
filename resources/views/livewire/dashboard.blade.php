<div x-data="calendarApp()">

    <x-ez-card class="mb-4">
        <x-slot name="body">
            <h1>Valamar</h1>
            <button wire:click="testActivityLog()" class="btn btn-sm">Log Test</button>
        </x-slot>
    </x-ez-card>


    <div class="mb-5 grid-cols-1  gap-y-4 md:grid-cols-6 md:gap-4 grid">

        @foreach($activites as  $activity)

            <div class="col-span-2">
                <x-ez-card>
                    <x-slot name="title" class="flex justify-between border-b border-base-300 pb-4">
                        <p>Log #ID {{$activity->id}}</p>
                    </x-slot>
                    <x-slot name="body">
                        <pre>{{json_encode($activity, JSON_PRETTY_PRINT)}}</pre>
                    </x-slot>

                </x-ez-card>
            </div>
        @endforeach
    </div>


    @include('packages.fullcalendar')
    <script>
    </script>

</div>

