<div {{ $attributes->merge(['class' => 'card bg-base-100 border shadow-md overflow-x-auto rounded-lg']) }}>

    <div  {{ $body->attributes->merge(['class' => 'card-body p-4']) }}>

        @if (!empty($title))
            <h2 {{$title->attributes->merge(['class' => 'card-title items-center '])}}>
                {{ $title }}
            </h2>
        @endif
        {{$body}}
    </div>
</div>
