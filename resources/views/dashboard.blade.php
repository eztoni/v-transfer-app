<x-app-layout>
    <x-ez-card>
        <x-slot name="body">
            <div id="calendar"></div>
          @php

                echo $rate;
            @endphp


        </x-slot>
    </x-ez-card>

</x-app-layout>
<script>

        var calendar = new window.Calendar('#calendar', {
            defaultView: 'month',
            taskView: true,
        });

</script>
