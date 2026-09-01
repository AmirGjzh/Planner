@php
    $navLinks = [
        ['label' => __('Dashboard'), 'route' => 'dashboard', 'icon' => 'Home'],
        ['label' => __('Tasks'), 'route' => 'tasks', 'icon' => 'Clipboard'],
        ['label' => __('Plans'), 'route' => 'plans', 'icon' => 'Bullseye'],
        ['label' => __('Categories'), 'route' => 'categories', 'icon' => 'Folder'],
        ['label' => __('Reports'), 'route' => 'reports', 'icon' => 'Chart2'],
    ];
@endphp

<div class="flex h-16 m-2 mine-card justify-between items-center overflow-visible! sticky top-2 z-60">
    {{-- Desktop navigation --}}
    <div class="hidden md:flex items-center">
        <div @class([
            "pl-6" => app()->isLocale('en'),
            "pr-6" => app()->isLocale('fa'),
            "flex items-center"
        ])>
            <a wire:navigate.hover href="{{ route('home') }}">
                <x-mine.brand-logo width="100" aria-label="{{ config('app.name') }}" />
            </a>
        </div>
        <div @class([
            "pl-6 pr-4" => app()->isLocale('en'),
            "pr-6 pl-4" => app()->isLocale('fa'),
            "flex gap-2 truncate min-w-0"
        ])>
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
            <x-mine.dropdown.trigger @class([
                "ml-2" => app()->isLocale('en'),
                "mr-2" => app()->isLocale('fa'),
                "pointer-events-auto"
            ])>
                <div class="mine-btn-icon flex items-center justify-center py-2 px-2 rounded-xl">
                    <div class="relative block size-6">
                        <div :style="open ? 'opacity:0; transform: rotate(90deg) scale(0.75);' : 'opacity:1; transform: rotate(0deg) scale(1);'"
                            style="opacity:1; transform: rotate(0deg) scale(1);"
                            class="absolute inset-0 flex flex-col items-center justify-center transition-all duration-200 ease-out">
                            <x-mine.icon name="MoreH" size="28" weight="filled" />
                        </div>
                        <div :style="open ? 'opacity:1; transform: rotate(0deg) scale(1);' : 'opacity:0; transform: rotate(-90deg) scale(0.75);'"
                            style="opacity:0; transform: rotate(-90deg) scale(0.75);"
                            class="absolute inset-0 flex items-center justify-center transition-all duration-200 ease-out">
                            <x-mine.icon name="Xmark" size="20" weight="filled" />
                        </div>
                    </div>
                </div>
            </x-mine.dropdown.trigger>

            <x-mine.dropdown.content class="mt-2! w-full pointer-events-auto"
                placement="bottom-{{ app()->isLocale('en') ? 'start' : 'end' }}">
                @foreach ($navLinks as $link)
                    <x-mine.dropdown.item :route="$link['route']">
                        <div class="w-full py-3 px-3 flex items-center justify-between hover:cursor-pointer">
                            <div class="flex gap-3 items-center">
                                <x-mine.icon :name="$link['icon']" weight="filled" size="20" />
                                <p @class([
                                    "pt-1" => app()->isLocale('en'),
                                    "pt-0.5" => app()->isLocale('fa'),
                                    "text-sm font-medium"
                                ])>{{ $link['label'] }}</p>
                            </div>
                            <x-mine.icon name="Angle{{ app()->isLocale('en') ? 'Right' : 'Left' }}" weight="filled" size="20" />
                        </div>
                    </x-mine.dropdown.item>
                @endforeach
            </x-mine.dropdown.content>
        </x-mine.dropdown>
    </div>

    <div class="flex items-center md:hidden mx-auto">
        <a wire:navigate.hover href="{{ route('home') }}">
            <x-mine.brand-logo width="100" aria-label="{{ config('app.name') }}" />
        </a>
    </div>

    {{-- Right actions --}}
    <div class="absolute inset-0 z-10 pointer-events-none">
        @auth
                    @php
                        $user = auth()->user();
                        $initials = $user->initials();
                    @endphp

                    <div class="w-full h-full flex items-center justify-end" x-data="{
                                        username: @js($user->username),
                                        email: @js($user->email),
                                        initials: @js($initials),
                                    }" x-on:profile-updated.window="
                                        username = $event.detail.username;
                                        email = $event.detail.email;
                                        initials = $event.detail.initials;
                                    ">
                        <div @class([
                            "pr-2" => app()->isLocale('en'),
                            "pl-2" => app()->isLocale('fa'),
                            "flex items-center"
                        ])>
                            <x-mine.theme-switcher class="pointer-events-auto" />
                        </div>
                        <x-mine.dropdown group="header-action" class="flex items-center h-full pointer-events-none">
                            <x-mine.dropdown.trigger class="pointer-events-auto">
                                <div @class([
                                    "pr-2 md:pr-4" => app()->isLocale('en'),
                                    "pl-2 md:pl-4" => app()->isLocale('fa'),
                                    "flex items-center hover:cursor-pointer rounded-xl py-1.5"
                                ])>
                                    <div @class([
                                        "mine-badge-primary rounded-xl size-11 flex items-center justify-center"
                                    ])>
                                        <h2 class="font-bold text-sm pt-0.5" x-text="initials">{{ $initials }}</h2>
                                    </div>
                                    <div class="hidden sm:flex items-center py-2 px-2 rounded-xl">
                                        <h2 @class([
                                            "mr-2 ml-1" => app()->isLocale('en'),
                                            "ml-2 mr-1" => app()->isLocale('fa'),
                                            "font-medium text-sm mine-text-secondary pt-0.5 min-w-0 max-w-25 truncate"
                                        ]) x-text="username">
                                            {{ $user->username }}
                                        </h2>
                                        <div class="relative size-4 mine-text-secondary">
                                            <div :class="!open ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
                                                class="absolute inset-0 transition-all duration-200 ease-out">
                                                <x-mine.icon name="AngleDown" weight="filled" size="16" />
                                            </div>
                                            <div :class="open ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
                                                class="absolute inset-0 transition-all duration-200 ease-out">
                                                <x-mine.icon name="AngleUp" weight="filled" size="16" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </x-mine.dropdown.trigger>

                            <x-mine.dropdown.content class="mt-2! min-w-0! pointer-events-auto"
                                placement="bottom-{{ app()->isLocale('fa') ? 'start' : 'end' }}">
                                <div @class([
                                    "pl-2 pr-4" => app()->isLocale('en'),
                                    "pr-2 pl-4" => app()->isLocale('fa'),
                                    "flex items-center gap-3 py-2"
                                ])>
                                    <div
                                        class="shrink-0 size-14 rounded-xl flex justify-center items-center text-sm font-medium mine-badge-primary">
                                        <h1 class="font-bold text-lg pt-1" x-text="initials">{{ $initials }}</h1>
                                    </div>
                                    <div class="min-w-0 max-w-60 truncate flex flex-col justify-between gap-1">
                                        <p class="text-sm font-semibold mine-text-primary" x-text="username">{{ $user->username }}
                                        </p>
                                        <p class="text-xs font-medium mine-text-secondary" x-text="email">{{ $user->email }}</p>
                                    </div>
                                </div>

                                <x-mine.dropdown.divider class="-mx-1" />

                                <x-mine.dropdown.item :route="'profile'">
                                    <div class="w-full py-3 px-3 flex items-center justify-between hover:cursor-pointer">
                                        <div class="flex gap-3 items-center">
                                            <x-mine.icon name="User4" size="20" weight="filled" />
                                            <p @class([
                                                "pt-1" => app()->isLocale('en'),
                                                "pt-0.5" => app()->isLocale('fa'),
                                                "text-sm font-medium"
                                            ])>{{ __('Profile') }}</p>
                                        </div>
                                        <x-mine.icon name="Angle{{ app()->isLocale('en') ? 'Right' : 'Left' }}" weight="filled" size="20" />
                                    </div>
                                </x-mine.dropdown.item>

                                <x-mine.dropdown.item>
                                    <div class="w-full py-3 px-3 flex items-center justify-between hover:cursor-pointer">
                                        <div class="flex gap-3 items-center">
                                            <x-mine.icon name="Gear" size="20" weight="filled" />
                                            <p @class([
                                                "pt-1" => app()->isLocale('en'),
                                                "pt-0.5" => app()->isLocale('fa'),
                                                "text-sm font-medium"
                                            ])>{{ __('Settings') }}</p>
                                        </div>
                                        <x-mine.icon name="Angle{{ app()->isLocale('en') ? 'Right' : 'Left' }}" weight="filled" size="20" />
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
                                                            if (window.Livewire?.navigate) {
                                                                window.Livewire.navigate('{{ route('home') }}');
                                                            } else {
                                                                window.location.href = '{{ route('home') }}';
                                                            }
                                                        })
                                                        .catch(() => busy = false);
                                                ">
                                    <div class="w-full py-3 px-3 mine-text-error flex items-center gap-3 hover:cursor-pointer">
                                        <x-mine.icon name="Logout4" weight="filled" size="18" />
                                        <p @class([
                                            "pt-1" => app()->isLocale('en'),
                                            "pt-0.5" => app()->isLocale('fa'),
                                            "text-sm font-medium"
                                        ])>{{ __('Log out') }}</p>
                                    </div>
                                </x-mine.dropdown.item>
                            </x-mine.dropdown.content>
                        </x-mine.dropdown>
                    </div>
        @else
            <div @class([
                "pr-3 md:pr-5" => app()->isLocale('en'),
                "pl-3 md:pl-5" => app()->isLocale('fa'),
                "w-full h-full pointer-events-none flex items-center justify-end gap-2"
            ])>
                <x-mine.theme-switcher class="pointer-events-auto" />
                <a wire:navigate.hover href="{{ route('login') }}" class="pointer-events-auto">
                    <x-mine.button class="mine-btn-primary sm:mine-btn-ghost text-sm" height="h-10">
                        <div @class([
                            "pb-1" => app()->isLocale('en'),
                            "flex items-center gap-2"
                        ])>
                            <p @class([
                                "pt-1" => app()->isLocale('en'),
                                "pb-0.5" => app()->isLocale('fa')
                            ])>{{ __('Sign in') }}</p>
                            <x-mine.icon name="Login4" weight="filled" size="18" class="sm:hidden" />
                        </div>
                    </x-mine.button>
                </a>
                <a wire:navigate.hover href="{{ route('register') }}" class="hidden sm:flex pointer-events-auto">
                    <x-mine.button class="mine-btn-primary text-sm" height="h-10">{{ __('Sign up') }}</x-mine.button>
                </a>
            </div>
        @endauth
    </div>
</div>