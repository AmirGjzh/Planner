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
        <x-ui.layout.header class="flex justify-end bg-gradient-to-r from-slate-100 to-slate-50">
            @auth
            <x-ui.dropdown>
                <x-slot:button class="bg-slate-100 p-1 rounded-lg mr-1">
                    <x-ui.icon name="user-circle" class="size-8"></x-ui.icon>
                </x-slot:button>
                <x-slot:menu>
                    <x-ui.dropdown.item wire:navigate href="/profile" icon="user">Profile</x-ui.dropdown.item>
                    <x-ui.dropdown.item variant="danger" icon="arrow-left-start-on-rectangle"
                        x-on:click="
                            fetch('{{ route('logout') }}', {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            }).then(() => Livewire.navigate('{{ route('login') }}'));
                        ">
                        Log out
                    </x-ui.dropdown.item>
                </x-slot:menu>
            </x-ui.dropdown>
            @endauth
        </x-ui.layout.header>

        <x-ui.layout.main>
            {{ $slot }}
        </x-ui.layout.main>
    </x-ui.layout>

    @livewireScripts
</body>

</html>
