<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'fa' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    @livewireScriptConfig
    <x-mine.theme-vars />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-dvh flex flex-col mine-page-bg font-sans">
    <x-mine.header />
    {{ $slot }}
    {{-- <x-mine.footer /> --}}
    <x-mine.toast />
</body>

</html>
