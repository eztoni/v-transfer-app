<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-error btn-sm']) }}>
    {{ $slot }}
</button>
