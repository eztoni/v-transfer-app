@props(['active'=>'','href'=>''])

@php
    $classes = ($active ?? false)
                ? 'shadow-lg rounded-lg border-primary bg-primary text-base-content'
                : '';

@endphp


<li {{ $attributes->merge(['class' => $classes]) }}>


    <a href="{{$href}}" class="{{$active ? 'active':''}}"   >
        {{ $icon ?? '' }}
       <span class="pl-2"> {{ $slot }}</span>
    </a></li>
