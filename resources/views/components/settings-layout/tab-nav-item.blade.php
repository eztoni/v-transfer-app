@props([
    'icon'=>null,
    'handle',
    'label'
])

<li >
    <a @click="showHandle = '{{$handle}}'"
       :class="{'ds-active':showHandle == '{{$handle}}'}"
       class=" rounded">
        @if($icon)
        <x-icon name="{{$icon}}" class="w-5 h-5"/>
        @endif
        {{$label}}
    </a>
</li>
