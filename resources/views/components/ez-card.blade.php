<div {{ $attributes->merge(['class' => 'card bg-base-100 shadow-lg overflow-x-auto']) }}>

    <div  {{ $body->attributes->merge(['class' => 'card-body']) }}>

        @if (!empty($title))
            <h2 {{$title->attributes->merge(['class' => 'card-title'])}}>
                {{ $title }}
            </h2>
        @endif
        {{$body}}
    </div>
</div>
