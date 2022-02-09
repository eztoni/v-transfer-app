@props(['active'=>'','text'=>'','items'=>[]])


<div {{ $attributes->class(['collapse drawer-submenu collapse-arrow ']) }} >
    <input type="checkbox" {{$active?'checked':''}}>
    <div class="collapse-title   font-medium">
        {{ $slot  }}
        <span class="pl-2"> {{ $text }}</span>
    </div>
    <div class="collapse-content">
        @foreach($items as $item)
            <a href="{{$item['href']}}" class="{{$item['active']?'active text-neutral-content':''}}">
                <div class="sub-nav-item {{$item['active']?'shadow-lg   bg-primary  ':''}}">

                    {{$item['text']}}

                </div>
            </a>
        @endforeach

    </div>
</div>
