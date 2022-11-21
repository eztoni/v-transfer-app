@props(['active'=>'','href'=>''])
@php
    $classes = ($active ?? false)
                ? 'my-1  border-primary  text-neutral-content '
                : 'my-1';
@endphp

<li {{ $attributes->merge(['class' => $classes]) }}>
    <a href="{{$href}}" class=" rounded  {{$active ? 'active  bg-primary-500 shadow-inner  bg-primary-500  ':''}}">
       <span class="pl-2"> {{ $icon ?? '' }}</span>
        <span class="pl-2"> {{ $slot }}</span>
    </a>
</li>
