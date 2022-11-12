@props(['active'=>'','text'=>'','items'=>[]])


<div x-data="{active : {{ $active?'true':'false'}} }"
     :class="active?'bg-gray-300':''" {{ $attributes->class(['  my-1     rounded ds-collapse ds-drawer-submenu ds-collapse-arrow ']) }} >
    <input  x-model="active" type="checkbox"  {{$active?'checked':''}}>
    <div class="ds-collapse-title   font-medium">
        {{ $slot  }}
        <span class="pl-2 "> {{ $text }}</span>
    </div>
    <div class="ds-collapse-content bg-gray-200 ">
        @foreach($items as $item)
            @if($item['show'])
                <a href="{{$item['href']}}" class=" {{$item['active']?'active text-neutral-content':''}}">
                    <div class="ds-sub-nav-item rounded  {{$item['active']?' shadow-inner active  bg-primary-500  ':'bg-gray-200 '}}">

                        {{$item['text']}}

                    </div>
                </a>
            @endif
        @endforeach

    </div>
</div>
