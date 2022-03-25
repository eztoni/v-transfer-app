<div {{ $attributes->merge(['class' => 'card bg-base-100 shadow-lg overflow-x-auto']) }}>

    <div  {{ $body->attributes->merge(['class' => 'card-body p-4']) }}>

        @if (!empty($title))
            <h2 {{$title->attributes->merge(['class' => 'card-title mb-2'])}}>
                {{ $title }}
            </h2>
        @endif
        {{$body}}
    </div>
</div>
