@php
    $navLinks = [
        ['label' => __('Dashboard'), 'route' => 'dashboard', 'icon' => 'Home'],
        ['label' => __('Tasks'), 'route' => 'tasks', 'icon' => 'Clipboard'],
        ['label' => __('Plans'), 'route' => 'plans', 'icon' => 'Bullseye'],
        ['label' => __('Categories'), 'route' => 'categories', 'icon' => 'Folder'],
        ['label' => __('Reports'), 'route' => 'reports', 'icon' => 'Chart2'],
    ];
@endphp

<div class="mine-card sticky top-2 z-60 m-2 flex h-16 items-center justify-between overflow-visible!">
    {{-- Desktop navigation --}}
    <div class="hidden items-center md:flex">
        <div @class([
            'pl-6' => app()->isLocale('en'),
            'pr-6' => app()->isLocale('fa'),
            'flex items-center',
        ])>
            <a wire:navigate.hover href="{{ route('home') }}">
                <x-mine.brand-logo width="100" aria-label="{{ config('app.name') }}" />
            </a>
        </div>
        <div @class([
            'pl-6 pr-4' => app()->isLocale('en'),
            'pr-6 pl-4' => app()->isLocale('fa'),
            'flex gap-2 truncate min-w-0',
        ])>
            @foreach ($navLinks as $link)
                <x-mine.nav-link :href="route($link['route'])" :route="$link['route']">
                    {{ $link['label'] }}
                </x-mine.nav-link>
            @endforeach
        </div>
    </div>

    {{-- Mobile navigation dropdown --}}
    <div class="pointer-events-none absolute inset-0 flex items-center md:hidden">
        <x-mine.dropdown group="header-action" class="flex h-full w-full items-center">
            <x-mine.dropdown.trigger @class([
                'ml-2' => app()->isLocale('en'),
                'mr-2' => app()->isLocale('fa'),
                'pointer-events-auto',
            ])>
                <div class="mine-btn-icon flex items-center justify-center rounded-xl px-2 py-2">
                    <div class="relative block size-6">
                        <div
                            :style="open
                                ? 'opacity:0; transform: rotate(90deg) scale(0.75);'
                                : 'opacity:1; transform: rotate(0deg) scale(1);'"
                            style="opacity: 1; transform: rotate(0deg) scale(1)"
                            class="absolute inset-0 flex flex-col items-center justify-center transition-all duration-200 ease-out"
                        >
                            <x-mine.icon name="MoreH" size="24" weight="filled" />
                        </div>
                        <div
                            :style="open
                                ? 'opacity:1; transform: rotate(0deg) scale(1);'
                                : 'opacity:0; transform: rotate(-90deg) scale(0.75);'"
                            style="opacity: 0; transform: rotate(-90deg) scale(0.75)"
                            class="absolute inset-0 flex items-center justify-center transition-all duration-200 ease-out"
                        >
                            <x-mine.icon name="Xmark" size="20" weight="filled" />
                        </div>
                    </div>
                </div>
            </x-mine.dropdown.trigger>

            <x-mine.dropdown.content
                class="pointer-events-auto mt-2! w-full"
                placement="bottom-{{ app()->isLocale('en') ? 'start' : 'end' }}"
            >
                @foreach ($navLinks as $link)
                    <x-mine.dropdown.item :route="$link['route']">
                        <div class="flex w-full items-center justify-between px-3 py-3 hover:cursor-pointer">
                            <div class="flex items-center gap-3">
                                <x-mine.icon :name="$link['icon']" weight="filled" size="20" />
                                <p @class([
                                    'pt-1' => app()->isLocale('en'),
                                    'pt-0.5' => app()->isLocale('fa'),
                                    'text-sm font-medium',
                                ])>
                                    {{ $link['label'] }}
                                </p>
                            </div>
                            <x-mine.icon
                                name="Angle{{ app()->isLocale('en') ? 'Right' : 'Left' }}"
                                weight="filled"
                                size="20"
                            />
                        </div>
                    </x-mine.dropdown.item>
                @endforeach
            </x-mine.dropdown.content>
        </x-mine.dropdown>
    </div>

    <div class="mx-auto flex items-center md:hidden">
        <a wire:navigate.hover href="{{ route('home') }}">
            <x-mine.brand-logo width="100" aria-label="{{ config('app.name') }}" />
        </a>
    </div>

    {{-- Right actions --}}
    <div class="pointer-events-none absolute inset-0 z-10">
        @auth
            @php
                $user = auth()->user();
                $initials = $user->initials();
            @endphp

            <div
                class="flex h-full w-full items-center justify-end"
                x-data="{
                                        username: @js($user->username),
                                        email: @js($user->email),
                                        initials: @js($initials),
                                    }"
                x-on:profile-updated.window="
                    username = $event.detail.username;
                    email = $event.detail.email;
                    initials = $event.detail.initials;
                "
            >
                <x-mine.dropdown group="header-action" class="pointer-events-none flex h-full items-center">
                    <x-mine.dropdown.trigger class="pointer-events-auto">
                        <div @class([
                            'pr-2 md:pr-4' => app()->isLocale('en'),
                            'pl-2 md:pl-4' => app()->isLocale('fa'),
                            'flex items-center hover:cursor-pointer rounded-xl py-1.5',
                        ])>
                            <div @class([
                                'mine-badge-primary rounded-xl size-11 flex items-center justify-center',
                            ])>
                                <h2 class="pt-0.5 text-sm font-bold" x-text="initials">{{ $initials }}</h2>
                            </div>
                            <div class="hidden items-center rounded-xl px-2 py-2 sm:flex">
                                <h2
                                    @class([
                                        'mr-2 ml-1' => app()->isLocale('en'),
                                        'ml-2 mr-1' => app()->isLocale('fa'),
                                        'font-medium text-sm mine-text-secondary pt-0.5 min-w-0 max-w-25 truncate',
                                    ])
                                    x-text="username"
                                >
                                    {{ $user->username }}
                                </h2>
                                <div class="mine-text-secondary relative size-4">
                                    <div
                                        :class="! open ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
                                        class="absolute inset-0 transition-all duration-200 ease-out"
                                    >
                                        <x-mine.icon name="AngleDown" weight="filled" size="16" />
                                    </div>
                                    <div
                                        :class="open ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
                                        class="absolute inset-0 transition-all duration-200 ease-out"
                                    >
                                        <x-mine.icon name="AngleUp" weight="filled" size="16" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-mine.dropdown.trigger>

                    <x-mine.dropdown.content
                        class="pointer-events-auto mt-2! min-w-0!"
                        placement="bottom-{{ app()->isLocale('fa') ? 'start' : 'end' }}"
                    >
                        <div @class([
                            'pl-2 pr-4' => app()->isLocale('en'),
                            'pr-2 pl-4' => app()->isLocale('fa'),
                            'flex items-center gap-3 py-2',
                        ])>
                            <div class="mine-badge-primary flex size-14 shrink-0 items-center justify-center rounded-xl text-sm font-medium">
                                <h1 class="pt-1 text-lg font-bold" x-text="initials">{{ $initials }}</h1>
                            </div>
                            <div class="flex max-w-60 min-w-0 flex-col justify-between gap-1 truncate">
                                <p class="mine-text-primary text-sm font-semibold" x-text="username">
                                    {{ $user->username }}
                                </p>
                                <p class="mine-text-secondary text-xs font-medium" x-text="email">{{ $user->email }}</p>
                            </div>
                        </div>

                        <x-mine.dropdown.divider class="-mx-1" />

                        <x-mine.dropdown.item :route="'profile'">
                            <div class="flex w-full items-center justify-between px-3 py-3 hover:cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <x-mine.icon name="User4" size="20" weight="filled" />
                                    <p @class([
                                        'pt-1' => app()->isLocale('en'),
                                        'pt-0.5' => app()->isLocale('fa'),
                                        'text-sm font-medium',
                                    ])>
                                        {{ __('Profile') }}
                                    </p>
                                </div>
                                <x-mine.icon
                                    name="Angle{{ app()->isLocale('en') ? 'Right' : 'Left' }}"
                                    weight="filled"
                                    size="20"
                                />
                            </div>
                        </x-mine.dropdown.item>

                        <x-mine.dropdown.item>
                            <div class="flex w-full items-center justify-between px-3 py-3 hover:cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <x-mine.icon name="Gear" size="20" weight="filled" />
                                    <p @class([
                                        'pt-1' => app()->isLocale('en'),
                                        'pt-0.5' => app()->isLocale('fa'),
                                        'text-sm font-medium',
                                    ])>
                                        {{ __('Settings') }}
                                    </p>
                                </div>
                                <x-mine.icon
                                    name="Angle{{ app()->isLocale('en') ? 'Right' : 'Left' }}"
                                    weight="filled"
                                    size="20"
                                />
                            </div>
                        </x-mine.dropdown.item>

                        <x-mine.dropdown.divider class="-mx-1" />

                        <x-mine.dropdown.item
                            destructive
                            x-data="{ busy: false }"
                            x-on:click.prevent="
                                                    if (busy) return;
                                                    busy = true;
                                                    fetch('{{ route('logout') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                                                        .then((response) => {
                                                            if (! response.ok) throw new Error('Logout request failed');
                                                            window.location.assign('{{ route('home') }}');
                                                        })
                                                        .catch(() => busy = false);
                                                "
                        >
                            <div class="mine-text-error flex w-full items-center gap-3 px-3 py-3 hover:cursor-pointer">
                                <x-mine.icon name="Logout4" weight="filled" size="18" />
                                <p @class([
                                    'pt-1' => app()->isLocale('en'),
                                    'pt-0.5' => app()->isLocale('fa'),
                                    'text-sm font-medium',
                                ])>
                                    {{ __('Log out') }}
                                </p>
                            </div>
                        </x-mine.dropdown.item>
                    </x-mine.dropdown.content>
                </x-mine.dropdown>
            </div>
        @else
            <div @class([
                'pr-3 md:pr-5' => app()->isLocale('en'),
                'pl-3 md:pl-5' => app()->isLocale('fa'),
                'w-full h-full pointer-events-none flex items-center justify-end gap-2',
            ])>
                <a wire:navigate.hover href="{{ route('login') }}" class="pointer-events-auto">
                    <x-mine.button class="mine-btn-primary sm:mine-btn-ghost text-sm" height="h-10">
                        <div @class([
                            'pb-1' => app()->isLocale('en'),
                            'flex items-center gap-2',
                        ])>
                            <p @class([
                                'pt-1' => app()->isLocale('en'),
                                'pb-0.5' => app()->isLocale('fa'),
                            ])>
                                {{ __('Sign in') }}
                            </p>
                            <x-mine.icon name="Login4" weight="filled" size="18" class="sm:hidden" />
                        </div>
                    </x-mine.button>
                </a>
                <a wire:navigate.hover href="{{ route('register') }}" class="pointer-events-auto hidden sm:flex">
                    <x-mine.button class="mine-btn-primary text-sm" height="h-10">{{ __('Sign up') }}</x-mine.button>
                </a>
            </div>
        @endauth
    </div>
</div>
