@props(['active'=>'','href'=>''])
@php
    $classes = ($active ?? false)
                ? 'shadow-inner  border-primary bg-primary-500 text-neutral-content'
                : '';
@endphp

<li {{ $attributes->merge(['class' => $classes]) }}>
    <a href="{{$href}}" class="border-b {{$active ? 'active     bg-primary-500  ':''}}">
        {{ $icon ?? '' }}
        <span class="pl-2"> {{ $slot }}</span>
    </a>
</li>
