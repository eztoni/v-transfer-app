<div wire:model="show" class="{{ $show ? 'fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-white/35 pointer-events-auto' : 'hidden' }}">
    <!-- Overlay that prevents clicking on elements underneath -->
    <div class="absolute inset-0 bg-transparent pointer-events-none"></div>

    <!-- Modal Content -->
    <div class="bg-white p-4 rounded-lg shadow-lg text-center w-64">
        <p class="text-lg font-semibold">Saving reservation...</p>
        <div class="mt-2 flex justify-center">
            <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
        </div>
    </div>
</div>
