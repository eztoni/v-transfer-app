<!DOCTYPE html>
<html data-theme="ezbooker" x-data="{}" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <script>
        if (localStorage.theme) document.documentElement.setAttribute("data-theme", window.localStorage.theme);
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="/img/volarevic-fav.png" sizes="32x32">
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <!-- Styles -->
    @stack('styles')
    @stack('scripts')

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="https://use.fontawesome.com/c4773b38c8.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
    @livewireStyles
<!-- Scripts -->

    <script src="{{ mix('js/app.js') }}" defer></script>
    <script src="{{ mix('js/imports.js') }}"></script>
</head>
<body class="font-sans antialiased bg-base-200">
<div class="h-screen drawer drawer-mobile">

    <input id="main-menu" type="checkbox" @change="$refs['right-drawer'].checked = false" class="drawer-toggle">
    <main class=" block p-2 overflow-x-hidden   text-base-content  drawer-content">
        @include('components.navbar')
        {{ $slot }}
    </main>
    @include('components.main-drawer')

</div>
@include('components.right-drawer')
@stack('modals')
@livewireScripts
</body>
</html>
