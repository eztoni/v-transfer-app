@props(['active'=>'','text'=>'','items'=>[]])


<div {{ $attributes->class(['collapse drawer-submenu collapse-arrow ',' active text-neutral-content bg-primary '=>$active]) }} >

    <input type="checkbox" {{$active?'checked':''}} class="{{$active?'active-submenu':''}}">
    <div class="collapse-title   font-medium">
        {{ $slot  }}
        <span class="pl-2"> {{ $text }}</span>
    </div>
    <div class="collapse-content text-base-content">
        @foreach($items as $item)
            @if($item['show'])
            <a href="{{$item['href']}}" class="{{$item['active']?'active text-neutral-content':''}}">
                <div class="sub-nav-item {{$item['active']?'shadow-lg active  bg-primary  ':'bg-base-100'}}">

                    {{$item['text']}}

                </div>
            </a>
            @endif
        @endforeach

    </div>
</div>
