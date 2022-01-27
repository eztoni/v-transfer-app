<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-primary btn-sm']) }}>
    {{ $slot }}
</button>
