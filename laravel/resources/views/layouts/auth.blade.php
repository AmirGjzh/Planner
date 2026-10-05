<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'fa' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link
        rel="preload"
        as="font"
        type="font/woff2"
        crossorigin
        href="{{ \Illuminate\Support\Facades\Vite::asset('resources/fonts/Vazirmatn-Regular.woff2') }}"
    />

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />

    <title>{{ ($title ?? null) ? __($title).' — '.config('app.name') : config('app.name') }}</title>

    @livewireScriptConfig
    {{-- Auth surfaces are pre-personalization: always the app default theme, never a saved account theme. --}}
    <x-mine.theme-vars :theme="config('themes.default')" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="mine-page-bg flex min-h-dvh flex-col font-sans">
    {{ $slot }}
    <x-mine.toast />
</body>
</html>
