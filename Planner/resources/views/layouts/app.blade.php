<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    @livewireScriptConfig
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-dvh flex flex-col">
    <x-mine.header />
    {{ $slot }}


    {{-- <x-ui.layout variant="header-sidebar">
        <x-ui.layout.header class="flex bg-gradient-to-r from-slate-100 to-slate-50">
            @auth
            <div class="hidden md:w-full md:flex md:justify-between md:items-center md:gap-4 md:mx-4">
                <x-ui.brand class="text-slate-800 mr-8" href="{{ route('home') }}" name="Planner" />
                <x-ui.navbar class="flex-1">
                    <livewire:layouts.partials.nav-links variant="navbar" />
                </x-ui.navbar>
                <livewire:layouts.partials.user-dropdown />
            </div>


            <div class="md:hidden w-full flex mx-4 justify-between items-center gap-4 ">
                <x-ui.brand class="text-slate-600" href="{{ route('home') }}" name="Planner" />
                <x-ui.modal.trigger id="mobile-menu">
                    <x-ui.icon name="bars-3" class="size-8 opacity-80 hover:cursor-pointer"></x-ui.icon>
                </x-ui.modal.trigger>
            </div>
            <livewire:layouts.partials.mobile-menu auth />
            @else


            <div class="hidden md:w-full md:flex md:justify-between md:items-center md:gap-4 md:mx-4">
                <x-ui.brand class="text-slate-800 mr-8" href="{{ route('home') }}" name="Planner" />
                <x-ui.navbar class="flex-1">
                    <livewire:layouts.partials.nav-links variant="navbar" />
                </x-ui.navbar>
                <livewire:layouts.partials.guest-action />
            </div>


            <div class="md:hidden w-full flex mx-4 justify-between items-center gap-4 ">
                <x-ui.brand class="text-slate-600" href="{{ route('home') }}" name="Planner" />
                <x-ui.modal.trigger id="mobile-menu">
                    <x-ui.icon name="bars-3" class="size-8 opacity-80 hover:cursor-pointer"></x-ui.icon>
                </x-ui.modal.trigger>
            </div>
            <livewire:layouts.partials.mobile-menu />
            @endauth
        </x-ui.layout.header>

        <x-ui.layout.main>
            {{ $slot }}
        </x-ui.layout.main>
    </x-ui.layout> --}}
</body>

</html>
