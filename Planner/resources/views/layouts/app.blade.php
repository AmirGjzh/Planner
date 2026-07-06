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

<body>

    <div class="w-full h-18 px-8 flex items-center">
        <div class="pr-12">
            <div class="pr-3"></div>
            <h1 class="text-lg font-medium">Planner</h1>
        </div>
        <div>
            <x-mine.dropdown>
                <x-mine.dropdown.trigger class="flex items-center gap-3 rounded-xl px-2 py-2 hover:bg-[#F4FBF7]">
                    <div class="text-left">

                        <p class="text-sm font-semibold text-[#111827]">
                            John Doe
                        </p>

                        <p class="text-xs text-[#6B7280]">
                            john@example.com
                        </p>

                    </div>
                </x-mine.dropdown.trigger>

                <x-mine.dropdown.content>
                    <div class="p-2">

                        <div class="flex items-center gap-3 rounded-xl p-2">

                            <img src="https://i.pravatar.cc/80" class="size-11 rounded-full">

                            <div>

                                <p class="text-sm font-semibold">
                                    John Doe
                                </p>

                                <p class="text-xs text-[#6B7280]">
                                    john@example.com
                                </p>

                            </div>

                        </div>

                    </div>

                    <x-mine.dropdown.divider />

                    <x-mine.dropdown.item>

                        {{-- <x-heroicon-o-user class="size-5" /> --}}

                        <span>Profile</span>

                    </x-mine.dropdown.item>

                    <x-mine.dropdown.item>

                        {{-- <x-heroicon-o-cog-6-tooth class="size-5" /> --}}

                        <span>Settings</span>

                    </x-mine.dropdown.item>

                    <x-mine.dropdown.divider />

                    <x-mine.dropdown.item destructive>

                        {{-- <x-heroicon-o-arrow-left-on-rectangle class="size-5" /> --}}

                        <span>Logout</span>

                    </x-mine.dropdown.item>
                </x-mine.dropdown.content>
            </x-mine.dropdown>
        </div>
    </div>

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
