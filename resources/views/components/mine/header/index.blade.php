@php
    $navLinks = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'squares-plus'],
        ['label' => 'Tasks', 'route' => 'tasks', 'icon' => 'list-bullet'],
        ['label' => 'Plans', 'route' => 'plans', 'icon' => 'rocket-launch'],
        ['label' => 'Categories', 'route' => 'categories', 'icon' => 'folder'],
        ['label' => 'Reports', 'route' => 'report-page', 'icon' => 'chart-bar'],
    ];
@endphp

<div class="flex h-16 m-2 mine-card justify-between items-center overflow-visible! sticky top-2 z-60">
    {{-- Desktop navigation --}}
    <div class="hidden md:flex items-center">
        <div class="flex items-center pl-6">
            <a wire:navigate.hover href="{{ route('home') }}">
                <img src="{{ asset('storage/images/logo.svg') }}" alt="{{ config('app.name') }}" width="110">
            </a>
        </div>
        <div class="pl-6 flex gap-2 pr-4 truncate min-w-0">
            @foreach ($navLinks as $link)
                <x-mine.nav-link :href="route($link['route'])" :route="$link['route']">
                    {{ $link['label'] }}
                </x-mine.nav-link>
            @endforeach
        </div>
    </div>

    {{-- Mobile navigation dropdown --}}
    <div class="absolute inset-0 md:hidden flex items-center pointer-events-none">
        <x-mine.dropdown group="header-action" class="w-full h-full flex items-center">
            <x-mine.dropdown.trigger class="ml-2 pointer-events-auto">
                <div class="mine-btn-icon flex items-center justify-center py-2 px-2 rounded-xl">
                    <div class="relative block size-6">
                        <div :style="open ? 'opacity:0; transform: rotate(90deg) scale(0.75);' : 'opacity:1; transform: rotate(0deg) scale(1);'"
                            style="opacity:1; transform: rotate(0deg) scale(1);"
                            class="absolute inset-0 flex items-center justify-center transition-all duration-200 ease-out">
                            <x-mine.icon name="bars-3" variant="micro" class="size-6" />
                        </div>
                        <div :style="open ? 'opacity:1; transform: rotate(0deg) scale(1);' : 'opacity:0; transform: rotate(-90deg) scale(0.75);'"
                            style="opacity:0; transform: rotate(-90deg) scale(0.75);"
                            class="absolute inset-0 flex items-center justify-center transition-all duration-200 ease-out">
                            <x-mine.icon name="x-mark" variant="micro" class="size-6" />
                        </div>
                    </div>
                </div>
            </x-mine.dropdown.trigger>

            <x-mine.dropdown.content class="mt-2! w-full pointer-events-auto" placement="bottom-start">
                @foreach ($navLinks as $link)
                    <x-mine.dropdown.item href="{{ route($link['route']) }}">
                        <div
                            class="w-full py-3 px-3 mine-text-secondary flex items-center justify-between hover:cursor-pointer">
                            <div class="flex gap-3">
                                <x-mine.icon :name="$link['icon']" variant="micro" class="size-5" />
                                <p class="text-sm font-medium">{{ $link['label'] }}</p>
                            </div>
                            <x-mine.icon name="chevron-right" class="size-5" variant="micro" />
                        </div>
                    </x-mine.dropdown.item>
                @endforeach
            </x-mine.dropdown.content>
        </x-mine.dropdown>
    </div>

    <div class="flex items-center md:hidden mx-auto">
        <a wire:navigate.hover href="{{ route('home') }}">
            <img src="{{ asset('storage/images/logo.svg') }}" alt="{{ config('app.name') }}" width="110">
        </a>
    </div>

    {{-- Right actions --}}
    <div class="absolute inset-0 z-10 pointer-events-none">
        @auth
            @php
                $user = auth()->user();
                $initials = $user->firstname && $user->lastname
                    ? strtoupper(substr($user->firstname, 0, 1) . substr($user->lastname, 0, 1))
                    : strtoupper(substr($user->username, 0, 2));
            @endphp

            <div class="w-full h-full flex items-center justify-end" x-data="{
                        username: @js($user->username),
                        email: @js($user->email),
                        initials: @js($initials),
                    }" x-on:profile-updated.window="
                        username = $event.detail.username;
                        email = $event.detail.email;
                        initials = $event.detail.firstname && $event.detail.lastname
                            ? ($event.detail.firstname.charAt(0) + $event.detail.lastname.charAt(0)).toUpperCase()
                            : $event.detail.username.slice(0, 2).toUpperCase();
                    ">
                <x-mine.dropdown group="header-action" class="flex items-center h-full pointer-events-none">
                    <x-mine.dropdown.trigger class="pointer-events-auto">
                        <div class="flex items-center hover:cursor-pointer rounded-xl py-1.5 pr-2 md:pr-4">
                            <div class="mine-badge-primary rounded-xl size-11 flex items-center justify-center mr-1">
                                <h2 class="font-bold text-sm" x-text="initials">{{ $initials }}</h2>
                            </div>
                            <div class="hidden sm:flex items-center py-2 px-2 rounded-xl">
                                <h2 class="font-medium text-sm mine-text-secondary mr-2 min-w-0 max-w-25 truncate"
                                    x-text="username">
                                    {{ $user->username }}
                                </h2>
                                <div class="relative size-4 mine-text-secondary">
                                    <div :class="!open ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
                                        class="absolute inset-0 transition-all duration-200 ease-out">
                                        <x-mine.icon name="chevron-down" variant="micro" class="size-4" />
                                    </div>
                                    <div :class="open ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
                                        class="absolute inset-0 transition-all duration-200 ease-out">
                                        <x-mine.icon name="chevron-up" variant="micro" class="size-4" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-mine.dropdown.trigger>

                    <x-mine.dropdown.content class="mt-2! min-w-0! pointer-events-auto" placement="bottom-end">
                        <div class="flex items-center gap-3 p-2">
                            <div
                                class="shrink-0 size-14 rounded-xl flex justify-center items-center text-sm font-medium mine-badge-primary">
                                <h1 class="font-bold text-lg" x-text="initials">{{ $initials }}</h1>
                            </div>
                            <div class="min-w-0 max-w-60 truncate flex flex-col justify-between">
                                <p class="text-sm font-semibold mine-text-primary" x-text="username">{{ $user->username }}
                                </p>
                                <p class="text-xs font-medium mine-text-secondary" x-text="email">{{ $user->email }}</p>
                            </div>
                        </div>

                        <x-mine.dropdown.divider class="-mx-1" />

                        <x-mine.dropdown.item href="{{ route('profile') }}">
                            <div
                                class="w-full py-3 px-3 mine-text-secondary flex items-center justify-between hover:cursor-pointer">
                                <div class="flex gap-3">
                                    <x-mine.icon name="user" class="size-5" variant="micro" />
                                    <p class="text-sm font-medium">Profile</p>
                                </div>
                                <x-mine.icon name="chevron-right" class="size-5" variant="micro" />
                            </div>
                        </x-mine.dropdown.item>

                        <x-mine.dropdown.item>
                            <div
                                class="w-full py-3 px-3 mine-text-secondary flex items-center justify-between hover:cursor-pointer">
                                <div class="flex gap-3">
                                    <x-mine.icon name="cog-6-tooth" class="size-5" variant="micro" />
                                    <p class="text-sm font-medium">Settings</p>
                                </div>
                                <x-mine.icon name="chevron-right" class="size-5" variant="micro" />
                            </div>
                        </x-mine.dropdown.item>

                        <x-mine.dropdown.divider class="-mx-1" />

                        <x-mine.dropdown.item destructive x-data="{ busy: false }" x-on:click.prevent="
                                    if (busy) return;
                                    busy = true;
                                    fetch('{{ route('logout') }}', {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                    })
                                        .then((response) => {
                                            if (! response.ok) throw new Error('Logout request failed');
                                            window.Livewire?.navigate('{{ route('home') }}') ?? (window.location.href = '{{ route('home') }}');
                                        })
                                        .catch(() => busy = false);
                                ">
                            <div class="w-full py-3 px-4 mine-text-error flex items-center gap-3 hover:cursor-pointer">
                                <x-mine.icon name="arrow-right-start-on-rectangle" class="size-5" variant="micro" />
                                <p class="text-sm font-medium">Logout</p>
                            </div>
                        </x-mine.dropdown.item>
                    </x-mine.dropdown.content>
                </x-mine.dropdown>
            </div>
        @else
            <div class="w-full h-full pointer-events-none flex items-center justify-end gap-2 pr-4 md:pr-6">
                <a wire:navigate href="{{ route('login') }}" class="pointer-events-auto">
                    <x-mine.button class="mine-btn-primary sm:mine-btn-ghost" height="h-10 text-sm">Sign in</x-mine.button>
                </a>
                <a wire:navigate href="{{ route('register') }}" class="hidden sm:flex pointer-events-auto">
                    <x-mine.button class="mine-btn-primary" height="h-10 text-sm">Sign up</x-mine.button>
                </a>
            </div>
        @endauth
    </div>
</div>