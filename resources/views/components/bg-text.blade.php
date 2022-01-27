<div class=" flex justify-between">
    <div class="px-6 py-2">
        <h3 class="text-lg font-medium text-base-content">{{ $slot }}</h3>

        <p class="mt-1 text-sm text-base-content">
            {{ $description ?? '' }}
        </p>
    </div>

    <div class="px-4 sm:px-0">
        {{ $aside ?? '' }}
    </div>
</div>
