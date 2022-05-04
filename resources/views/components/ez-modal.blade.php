@props([
    'id' => 'ez-modal',
    'isOpen' =>false,
    'lg'=>false
])


@push('modals')

    @if (!empty($button))
        <label for="{{$id}}" {{$button->attributes->merge(['class' => 'btn'])}}>{{$button}}</label>
    @endif
    <input type="checkbox" id="{{$id}}" class="modal-toggle ">
    <label for="{{$id}}" {{ $attributes->class(['modal-open'=>$isOpen])->merge(['class' => 'modal cursor-pointer']) }}>
        <label class="modal-box relative {{$lg?'w-1000px max-w-5xl':''}} ">
            <label for="{{$id}}" class="btn btn-sm btn-circle absolute right-2 top-2">✕</label>

            {{$slot}}

            @if (!empty($footer))
                <div class="modal-action">
                    {{$footer ??''}}
                </div>
            @endif
        </label>
    </label>
@endpush
