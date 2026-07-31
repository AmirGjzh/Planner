@php $routeName = request()->route()?->getName(); @endphp

<div class=" flex h-16 m-2 mine-card justify-between items-center overflow-visible! sticky top-2 z-50">
    <div class="hidden md:flex items-center">
        {{-- LOGO --}}
        <div class="pl-4 pr-12 flex items-center shrink-0">
            <div class="pr-3"></div>
            <h1 class="text-lg font-medium">Planner</h1>
        </div>
        {{-- LINKS --}}
        <div class="flex gap-4 pr-4 truncate min-w-0">
            <x-mine.nav-link href="{{ route('dashboard') }}" :route="'dashboard'" :active="$routeName === 'dashboard'">
                Dashboard
            </x-mine.nav-link>

            <x-mine.nav-link href="{{ route('task-page') }}" :route="'task-page'" :active="$routeName === 'task-page'">
                Tasks
            </x-mine.nav-link>

            <x-mine.nav-link href="{{ route('plans') }}" :route="'plans'" :active="$routeName === 'plans'">
                Plans
            </x-mine.nav-link>

            <x-mine.nav-link href="{{ route('categories') }}" :route="'category-page'" :active="$routeName === 'categories'">
                Categories
            </x-mine.nav-link>

            <x-mine.nav-link href="{{ route('report-page') }}" :route="'report-page'" :active="$routeName === 'report-page'">
                Reports
            </x-mine.nav-link>
        </div>
    </div>
    <div class="hidden sm:flex items-center pr-6">
        <div class="pr-2">
            <x-mine.theme-switcher />
        </div>
        <x-mine.dropdown>
            <x-mine.dropdown.trigger>
                <div class="flex items-center mine-badge-primary py-2 px-2 rounded-xl">
                    <x-mine.icon name="user" />
                    <div class="relative size-4">
                        <div
                            :class="!open ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
                            class="absolute inset-0 transition-all duration-200 ease-out"
                        >
                            <x-mine.icon name="chevron-down" variant="mini" class="size-4" />
                        </div>
                        <div
                            :class="open ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
                            class="absolute inset-0 transition-all duration-200 ease-out"
                        >
                            <x-mine.icon name="chevron-up" variant="mini" class="size-4" />
                        </div>
                    </div>
                </div>
            </x-mine.dropdown.trigger>

            <x-mine.dropdown.content class="mt-1!">
                <div class="flex items-center gap-3 p-2">
                    <div class="shrink-0 size-14 rounded-xl flex justify-center items-center text-sm font-medium mine-badge-primary">
                        <h1 class="font-bold text-lg">{{ strtoupper(Auth()->user()->firstname && Auth()->user()->lastname ? substr(Auth()->user()->firstname, 0, 1) . substr(Auth()->user()->lastname, 0, 1) : substr(Auth()->user()->username, 0, 2)) }}</h1>
                    </div>
                    <div class="min-w-0 max-w-60 truncate flex flex-col justify-between">
                        <p class="text-md font-semibold mine-text-primary">
                            {{ auth()->user()->username }}
                        </p>
                        <p class="text-sm font-medium mine-text-secondary">
                            {{ auth()->user()->email }}
                        </p>
                    </div>
                </div>

                <x-mine.dropdown.divider class="-mx-1" />

                <x-mine.dropdown.item href="{{ route('profile') }}" class="mb-2">
                    <div class="w-full py-3 px-4 mine-text-link flex items-center gap-2 hover:cursor-pointer">
                        <x-mine.icon name="user" class="size-5 mr-1" variant="micro" />
                        <p class="text-md font-medium">Profile</p>
                    </div>
                </x-mine.dropdown.item>

                <x-mine.dropdown.item>
                    <div class="w-full py-3 px-4 mine-text-link flex items-center gap-2 hover:cursor-pointer">
                        <x-mine.icon name="cog-6-tooth" class="size-5 mr-1" variant="micro" />
                        <p class="text-md font-medium">Settings</p>
                    </div>
                </x-mine.dropdown.item>

                <x-mine.dropdown.divider class="-mx-1" />

                <x-mine.dropdown.item destructive x-data x-on:click.prevent="
                    fetch('{{ route('logout') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).then(() => window.Livewire?.navigate('{{ route('login') }}'))
                ">
                    <div class="w-full py-3 px-4 mine-text-error flex items-center gap-2 hover:cursor-pointer">
                        <x-mine.icon name="arrow-right-start-on-rectangle" class="size-5 mr-1" variant="micro" />
                        <p class="text-md font-medium">Logout</p>
                    </div>
                </x-mine.dropdown.item>
            </x-mine.dropdown.content>
        </x-mine.dropdown>
    </div>
</div>
