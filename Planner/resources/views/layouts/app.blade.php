<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body>

    <x-ui.layout variant="header-sidebar">
        <x-ui.layout.header class="bg-gradient-to-r from-slate-100 to-slate-50">
            <x-ui.navbar></x-ui.navbar>
        </x-ui.layout.header>

        <x-ui.layout.main>
            {{ $slot }}
        </x-ui.layout.main>
    </x-ui.layout>

    @livewireScripts
</body>

</html>
