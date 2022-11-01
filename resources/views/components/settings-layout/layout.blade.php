@props([
    'defaultHandle'
])


<div class="grid grid-cols-8 gap-2" x-data="{showHandle:'{{$defaultHandle}}'}">
    <div class="col-span-2">
        <x-card cardClasses="">
            @if(isset($navHeader))
                {{$navHeader}}
                <hr class="my-4">
            @endif

            <ul class="ds-menu bg-base-100   ">
                {{$navItems}}
            </ul>

        </x-card>

        </div>
    <div class="col-span-6">
        {{$tabHeader}}

        {{$slot}}

    </div>
</div>
