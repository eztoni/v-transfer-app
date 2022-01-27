@props(['id' => 'ez-modal'])

@if (!empty($button))
    <label for="{{$id}}" {{$button->attributes->merge(['class' => 'btn'])}}>{{$button}}</label>
@endif
<input type="checkbox" id="{{$id}}"  x-ref="{{$id}}" class="modal-toggle">
<div {{ $attributes->merge(['class' => 'modal']) }}>
    <div class="modal-box max-h-screen overflow-y-auto">
        {{$slot}}
        @if (!empty($footer))
            <div class="modal-action">
                {{$footer ??''}}
            </div>
        @endif
    </div>
</div>
