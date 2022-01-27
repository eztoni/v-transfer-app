@props(['active'])

@php
$classes = ($active ?? false)
            ? 'border-b-2 border-primary bg-primary text-base-content'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition';
@endphp


    <li {{ $attributes->merge(['class' => $classes]) }}>


        <a >
            {{ $svg ?? '' }}
    {{ $slot }}
        </a></li>
