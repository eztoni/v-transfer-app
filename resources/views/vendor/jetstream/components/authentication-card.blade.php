<div class="min-h-screen grid md:grid-cols-2 gap-0 sm:justify-center items-center pt-6 sm:pt-0 bg-base-200">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md  mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="flex justify-center my-5">
            {{ $logo }}
        </div>
        {{ $slot }}
    </div>
</div>
