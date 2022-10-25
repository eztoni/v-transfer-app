<!DOCTYPE html>
<html data-theme="light" x-data="{}" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="/img/favicon.ico" sizes="32x32">
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <!-- Styles -->
    @vite('resources/css/app.css')
    @vite('resources/css/styles.scss')



    @stack('styles')
    @stack('scripts')

    <script src="https://use.fontawesome.com/c4773b38c8.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">

    @livewireStyles
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ Config::get('ez.google_maps_api_key') }}&libraries=places"
        async defer></script>
    @vite('resources/js/imports.js')
    <wireui:scripts />
    @vite('resources/js/app.js')

</head>
<body class="font-sans antialiased " style="background-color: rgb(229 230 230 / 47%);">
<div class="h-screen  ds-drawer  ds-drawer-mobile">

    <input id="main-menu" type="checkbox" @change="$refs['right-drawer'].checked = false" class=" ds-drawer-toggle">

    <x-main-drawer></x-main-drawer>

    <main class=" block p-2 overflow-x-hidden   text-base-content   ds-drawer-content ">
        @include('components.navbar')
        <div >
            {{ $slot }}
        </div>
    </main>


</div>
<div class="notif-wrapper">
    <x-notifications />
</div>
@include('components.right-drawer')
@stack('modals')
@stack('scripts-bottom')
@livewireScripts
</body>
</html>
