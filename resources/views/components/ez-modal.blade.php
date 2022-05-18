@props([
    'id' => 'ez-modal',
    'isOpen' =>false,
    'lg'=>false
])



    @if (!empty($button))
        <label for="{{$id}}" {{$button->attributes->merge(['class' => 'btn'])}}>{{$button}}</label>
    @endif
    <div {{ $attributes->class(['modal-open'=>$isOpen])->merge(['class' => 'modal']) }}>
        <div class="modal-box relative {{$lg?'w-1000px max-w-5xl':''}} ">
            {{$slot}}
            @if (!empty($footer))
                <div class="modal-action">
                    {{$footer ??''}}
                </div>
            @endif
        </div>
    </div>
