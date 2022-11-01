@props([
    'handle'
])

<div x-show="showHandle == '{{$handle}}'" x-transition x-transition:leave="absolute hidden">
        {{$slot}}
</div>
