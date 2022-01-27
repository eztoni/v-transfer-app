<x-app-layout>
    <x-ez-card>
        <x-slot name="body">
            <div id="calendar"></div>

        </x-slot>
    </x-ez-card>

</x-app-layout>
<script>

        var calendar = new window.Calendar('#calendar', {
            defaultView: 'month',
            taskView: true,
        });

</script>
