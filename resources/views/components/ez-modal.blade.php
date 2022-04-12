@props([
    'id' => 'ez-modal',
    'isOpen' =>false
])



@if (!empty($button))
    <label for="{{$id}}" {{$button->attributes->merge(['class' => 'btn'])}}>{{$button}}</label>
@endif
<input type="checkbox" id="{{$id}}"   class="modal-toggle ">
<label for="{{$id}}" {{ $attributes->class(['modal-open'=>$isOpen])->merge(['class' => 'modal cursor-pointer']) }}>
    <label class="modal-box relative ">
        <label for="{{$id}}" class="btn btn-sm btn-circle absolute right-2 top-2">✕</label>

        {{$slot}}

        @if (!empty($footer))
            <div class="modal-action">
                {{$footer ??''}}
            </div>
        @endif
    </label>
</label>
