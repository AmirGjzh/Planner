@php $routeName = request()->route()?->getName(); @endphp

<div class=" flex h-16 m-2 mine-card justify-between items-center overflow-visible! sticky top-2 z-50">
    @auth
        <div class="hidden md:flex items-center">
            {{-- LOGO --}}
            <div class="hidden pl-4 pr-4 items-center shr">
                <img src="{{ asset('storage/images/Logo.png') }}" alt="Planner" width="120">
            </div>
            {{-- LINKS --}}
            <div class="pl-4 flex gap-2 pr-4 truncate min-w-0">
                <x-mine.nav-link href="{{ route('dashboard') }}" :route="'dashboard'" :active="$routeName === 'dashboard'">
                    Dashboard
                </x-mine.nav-link>

                <x-mine.nav-link href="{{ route('task-page') }}" :route="'task-page'" :active="$routeName === 'task-page'">
                    Tasks
                </x-mine.nav-link>

                <x-mine.nav-link href="{{ route('plans') }}" :route="'plans'" :active="$routeName === 'plans'">
                    Plans
                </x-mine.nav-link>

                <x-mine.nav-link href="{{ route('categories') }}" :route="'category-page'"
                    :active="$routeName === 'categories'">
                    Categories
                </x-mine.nav-link>

                <x-mine.nav-link href="{{ route('report-page') }}" :route="'report-page'"
                    :active="$routeName === 'report-page'">
                    Reports
                </x-mine.nav-link>
            </div>
        </div>
        <div class="absolute inset-0 md:hidden flex items-center">
            <x-mine.dropdown group="header-action" class="w-full h-full flex items-center">
                <x-mine.dropdown.trigger class="ml-2">
                    <div class="flex items-center justify-center py-2 px-2 rounded-xl mine-btn-icon">
                        <div :class="!open ? 'opacity-100 scale-100' : 'hidden scale-75'"
                            class="transition-all duration-200 ease-out">
                            <x-mine.icon name="bars-3" variant="micro" class="size-6" />
                        </div>
                        <div :class="open ? 'opacity-100 scale-100' : 'hidden scale-75'"
                            class="transition-all duration-200 ease-out">
                            <x-mine.icon name="x-mark" variant="micro" class="size-6" />
                        </div>
                    </div>
                </x-mine.dropdown.trigger>

                <x-mine.dropdown.content class="mt-2! w-full" placement="bottom-start">
                    <x-mine.dropdown.item>
                        <div
                            class="w-full py-3 px-3 mine-text-secondary flex items-center justify-between hover:cursor-pointer">
                            <div class="flex gap-2">
                                <p class="text-sm font-medium">Dashboard</p>
                            </div>
                            <x-mine.icon name="chevron-right" class="size-5" variant="micro" />
                        </div>
                    </x-mine.dropdown.item>

                    <x-mine.dropdown.item>
                        <div
                            class="w-full py-3 px-3 mine-text-secondary flex items-center justify-between hover:cursor-pointer">
                            <div class="flex gap-2">
                                <p class="text-sm font-medium">Tasks</p>
                            </div>
                            <x-mine.icon name="chevron-right" class="size-5" variant="micro" />
                        </div>
                    </x-mine.dropdown.item>

                    <x-mine.dropdown.item>
                        <div
                            class="w-full py-3 px-3 mine-text-secondary flex items-center justify-between hover:cursor-pointer">
                            <div class="flex gap-2">
                                <p class="text-sm font-medium">Plans</p>
                            </div>
                            <x-mine.icon name="chevron-right" class="size-5" variant="micro" />
                        </div>
                    </x-mine.dropdown.item>

                    <x-mine.dropdown.item href="{{ route('categories') }}">
                        <div
                            class="w-full py-3 px-3 mine-text-secondary flex items-center justify-between hover:cursor-pointer">
                            <div class="flex gap-2">
                                <p class="text-sm font-medium">Categories</p>
                            </div>
                            <x-mine.icon name="chevron-right" class="size-5" variant="micro" />
                        </div>
                    </x-mine.dropdown.item>

                    <x-mine.dropdown.item>
                        <div
                            class="w-full py-3 px-3 mine-text-secondary flex items-center justify-between hover:cursor-pointer">
                            <div class="flex gap-2">
                                <p class="text-sm font-medium">Reports</p>
                            </div>
                            <x-mine.icon name="chevron-right" class="size-5" variant="micro" />
                        </div>
                    </x-mine.dropdown.item>
                </x-mine.dropdown.content>
            </x-mine.dropdown>
        </div>
        <div class="hidden items-center mx-auto">
            <img src="{{ asset('storage/images/Logo.png') }}" alt="Planner" width="120">
        </div>
        <div class="absolute inset-0 z-10 pointer-events-none">
            <x-mine.dropdown group="header-action"
                class="w-full h-full flex items-center justify-end pr-2 pointer-events-none">
                <x-mine.dropdown.trigger class="pointer-events-auto">
                    <div
                        class="flex items-center  hover:cursor-pointer rounded-xl py-1.5 sm:px-2">
                        <div class="mine-badge-primary rounded-xl size-11 flex items-center justify-center mr-1">
                            <h2 class="font-bold text-sm">
                                {{ strtoupper(auth()->user()->firstname && auth()->user()->lastname ? substr(auth()->user()->firstname, 0, 1) . substr(auth()->user()->lastname, 0, 1) : substr(auth()->user()->username, 0, 2)) }}
                            </h2>
                        </div>
                        <div class="hidden sm:flex items-center py-2 px-2 rounded-xl">
                            <h2 class=" font-medium text-sm mine-text-secondary mr-2 min-w-0 max-w-25 truncate">
                                {{ auth()->user()->username }}
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
                            <h1 class="font-bold text-lg">
                                {{ strtoupper(Auth()->user()->firstname && Auth()->user()->lastname ? substr(Auth()->user()->firstname, 0, 1) . substr(Auth()->user()->lastname, 0, 1) : substr(Auth()->user()->username, 0, 2)) }}
                            </h1>
                        </div>
                        <div class="min-w-0 max-w-60 truncate flex flex-col justify-between">
                            <p class="text-sm font-semibold mine-text-primary">
                                {{ auth()->user()->username }}
                            </p>
                            <p class="text-xs font-medium mine-text-secondary">
                                {{ auth()->user()->email }}
                            </p>
                        </div>
                    </div>

                    <x-mine.dropdown.divider class="-mx-1" />

                    <x-mine.dropdown.item href="{{ route('profile') }}" class="mb-2">
                        <div
                            class="w-full py-3 px-3 mine-text-secondary flex items-center justify-between hover:cursor-pointer">
                            <div class="flex gap-2">
                                <x-mine.icon name="user" class="size-5 mr-1" variant="micro" />
                                <p class="text-sm font-medium">Profile</p>
                            </div>
                            <x-mine.icon name="chevron-right" class="size-5" variant="micro" />
                        </div>
                    </x-mine.dropdown.item>

                    <x-mine.dropdown.item>
                        <div
                            class="w-full py-3 px-3 mine-text-secondary flex items-center justify-between hover:cursor-pointer">
                            <div class="flex gap-2">
                                <x-mine.icon name="cog-6-tooth" class="size-5 mr-1" variant="micro" />
                                <p class="text-sm font-medium">Settings</p>
                            </div>
                            <x-mine.icon name="chevron-right" class="size-5" variant="micro" />
                        </div>
                    </x-mine.dropdown.item>

                    <x-mine.dropdown.divider class="-mx-1" />

                    <x-mine.dropdown.item destructive x-data x-on:click.prevent="
                                fetch('{{ route('logout') }}', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                }).then(() => window.Livewire?.navigate('{{ route('home') }}'))
                            ">
                        <div class="w-full py-3 px-4 mine-text-error flex items-center gap-2 hover:cursor-pointer">
                            <x-mine.icon name="arrow-right-start-on-rectangle" class="size-5 mr-1" variant="micro" />
                            <p class="text-sm font-medium">Logout</p>
                        </div>
                    </x-mine.dropdown.item>
                </x-mine.dropdown.content>
            </x-mine.dropdown>
        </div>
    @else
        <div class="hidden md:flex items-center">
            {{-- LOGO --}}
            <div class="hidden pl-4 pr-6 items-center shr">
                <img src="{{ asset('storage/images/Logo.png') }}" alt="Planner" width="120">
            </div>
            {{-- LINKS --}}
            <div class="pl-4 flex gap-2 pr-4 truncate min-w-0">
                <x-mine.nav-link href="{{ route('dashboard') }}" :route="'dashboard'" :active="$routeName === 'dashboard'">
                    Dashboard
                </x-mine.nav-link>

                <x-mine.nav-link href="{{ route('task-page') }}" :route="'task-page'" :active="$routeName === 'task-page'">
                    Tasks
                </x-mine.nav-link>

                <x-mine.nav-link href="{{ route('plans') }}" :route="'plans'" :active="$routeName === 'plans'">
                    Plans
                </x-mine.nav-link>

                <x-mine.nav-link href="{{ route('categories') }}" :route="'category-page'"
                    :active="$routeName === 'categories'">
                    Categories
                </x-mine.nav-link>

                <x-mine.nav-link href="{{ route('report-page') }}" :route="'report-page'"
                    :active="$routeName === 'report-page'">
                    Reports
                </x-mine.nav-link>
            </div>
        </div>
        <div class="absolute inset-0 md:hidden flex items-center">
            <x-mine.dropdown group="header-action" class="w-full h-full flex items-center">
                <x-mine.dropdown.trigger class="ml-2">
                    <div class="flex items-center justify-center py-2 px-2 rounded-xl mine-btn-icon">
                        <div :class="!open ? 'opacity-100 scale-100' : 'hidden scale-75'"
                            class="transition-all duration-200 ease-out">
                            <x-mine.icon name="bars-3" variant="micro" class="size-6" />
                        </div>
                        <div :class="open ? 'opacity-100 scale-100' : 'hidden scale-75'"
                            class="transition-all duration-200 ease-out">
                            <x-mine.icon name="x-mark" variant="micro" class="size-6" />
                        </div>
                    </div>
                </x-mine.dropdown.trigger>

                <x-mine.dropdown.content class="mt-2! w-full" placement="bottom-start">
                    <x-mine.dropdown.item>
                        <div
                            class="w-full py-3 px-3 mine-text-secondary flex items-center justify-between hover:cursor-pointer">
                            <div class="flex gap-2">
                                <p class="text-sm font-medium">Dashboard</p>
                            </div>
                            <x-mine.icon name="chevron-right" class="size-5" variant="micro" />
                        </div>
                    </x-mine.dropdown.item>

                    <x-mine.dropdown.item>
                        <div
                            class="w-full py-3 px-3 mine-text-secondary flex items-center justify-between hover:cursor-pointer">
                            <div class="flex gap-2">
                                <p class="text-sm font-medium">Tasks</p>
                            </div>
                            <x-mine.icon name="chevron-right" class="size-5" variant="micro" />
                        </div>
                    </x-mine.dropdown.item>

                    <x-mine.dropdown.item>
                        <div
                            class="w-full py-3 px-3 mine-text-secondary flex items-center justify-between hover:cursor-pointer">
                            <div class="flex gap-2">
                                <p class="text-sm font-medium">Plans</p>
                            </div>
                            <x-mine.icon name="chevron-right" class="size-5" variant="micro" />
                        </div>
                    </x-mine.dropdown.item>

                    <x-mine.dropdown.item href="{{ route('categories') }}">
                        <div
                            class="w-full py-3 px-3 mine-text-secondary flex items-center justify-between hover:cursor-pointer">
                            <div class="flex gap-2">
                                <p class="text-sm font-medium">Categories</p>
                            </div>
                            <x-mine.icon name="chevron-right" class="size-5" variant="micro" />
                        </div>
                    </x-mine.dropdown.item>

                    <x-mine.dropdown.item>
                        <div
                            class="w-full py-3 px-3 mine-text-secondary flex items-center justify-between hover:cursor-pointer">
                            <div class="flex gap-2">
                                <p class="text-sm font-medium">Reports</p>
                            </div>
                            <x-mine.icon name="chevron-right" class="size-5" variant="micro" />
                        </div>
                    </x-mine.dropdown.item>
                </x-mine.dropdown.content>
            </x-mine.dropdown>
        </div>
        <div class="md:hidden flex items-center mx-auto">
            <img class="hidden" src="{{ asset('storage/images/Logo.png') }}" alt="Planner" width="120">
        </div>
        <div class="relative z-10 flex items-center justify-between gap-2 pr-4">
            <a wire:navigate href="{{ route('login') }}"><x-mine.button class="mine-btn-primary sm:mine-btn-ghost"
                    height="h-10 text-sm">Sign in</x-mine.button>
            </a>
            <a wire:navigate href="{{ route('register') }}" class="hidden sm:flex">
                <x-mine.button class="mine-btn-primary"
                    height="h-10 text-sm">Sign up</x-mine.button>
            </a>
        </div>
    @endauth
</div>