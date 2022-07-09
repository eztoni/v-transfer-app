@props(['active'=>'','text'=>'','items'=>[]])


<div x-data="{active : {{ $active?'true':'false'}} }" :class="active?'bg-gray-100':''" {{ $attributes->class(['  border-b ds-collapse ds-drawer-submenu ds-collapse-arrow ']) }} >
    <input  x-model="active" type="checkbox"  {{$active?'checked':''}}>
    <div class="ds-collapse-title   font-medium">
        {{ $slot  }}
        <span class="pl-2 "> {{ $text }}</span>
    </div>
    <div class="ds-collapse-content">
        @foreach($items as $item)
            @if($item['show'])
                <a href="{{$item['href']}}" class=" {{$item['active']?'active text-neutral-content':''}}">
                    <div class="ds-sub-nav-item border-t  {{$item['active']?' shadow-inner   bg-primary-500  ':''}}">

                        {{$item['text']}}

                    </div>
                </a>
            @endif
        @endforeach

    </div>
</div>
