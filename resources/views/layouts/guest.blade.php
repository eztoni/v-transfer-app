<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'valamar Aplikacija') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link rel="icon" href="/img/favicon.ico" sizes="32x32">

        <!-- Styles -->
        @vite('resources/css/app.css')
    @livewireStyles

        <!-- Scripts -->
        @vite('resources/js/app.js')
    </head>
    <body  data-theme="valamar">
        <div class="font-sans text-gray-900  antialiased">
            {{ $slot }}
        </div>

        @livewireScripts

    </body>
</html>
