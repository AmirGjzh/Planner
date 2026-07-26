@php $routeName = request()->route()?->getName(); @endphp

<div class=" flex  h-16 px-8 m-2 mine-card justify-between items-center overflow-visible! z-40">
    <div class="hidden sm:flex items-center">
        {{-- LOGO --}}
        <div class="pr-12">
            <div class="pr-3"></div>
            <h1 class="text-lg font-medium">Planner</h1>
        </div>
        {{-- LINKS --}}
        <div class="flex gap-4 pr-4">
            <x-mine.nav-link href="{{ route('dashboard') }}" :route="'dashboard'" :active="$routeName === 'dashboard'">
                Dashboard
            </x-mine.nav-link>

            <x-mine.nav-link href="{{ route('task-page') }}" :route="'task-page'" :active="$routeName === 'task-page'">
                Tasks
            </x-mine.nav-link>

            <x-mine.nav-link href="{{ route('plans') }}" :route="'plans'" :active="$routeName === 'plans'">
                Plans
            </x-mine.nav-link>

            <x-mine.nav-link href="{{ route('categories') }}" :route="'category-page'" :active="$routeName === 'category-page'">
                Categories
            </x-mine.nav-link>

            <x-mine.nav-link href="{{ route('report-page') }}" :route="'report-page'" :active="$routeName === 'report-page'">
                Reports
            </x-mine.nav-link>
        </div>
    </div>
    <div class="hidden sm:flex items-center">
        <div class="pr-2">
            {{-- <x-mine.theme-switcher /> --}}
        </div>
        <x-mine.dropdown trigger-mode="hover">
            <x-mine.dropdown.trigger class="h-10 flex items-center rounded-xl p-2 hover:cursor-pointer text-[var(--mine-theme-switcher-color)] hover:bg-[var(--mine-theme-switcher-bg-hover)]">
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
            </x-mine.dropdown.trigger>

            <x-mine.dropdown.content>
                <div class="flex items-center gap-4 rounded-xl p-2">
                    <div class="size-12 rounded-xl bg-[var(--mine-theme-switcher-bg-hover)] text-[var(--mine-theme-switcher-color)] flex justify-center items-center text-sm font-medium leading-none">
                        {{ strtoupper(substr(auth()->user()->username, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[var(--mine-text-primary)]">
                            {{ auth()->user()->username }}
                        </p>
                        <p class="text-xs font-medium text-[var(--mine-text-secondary)]">
                            {{ auth()->user()->email }}
                        </p>
                    </div>
                </div>

                <x-mine.dropdown.divider />

                <x-mine.dropdown.item href="{{ route('profile') }}" class="mb-2">
                    <x-mine.icon name="identification" class="mr-3 text-[var(--mine-text-primary)]" />
                    <p class="text-sm text-[var(--mine-text-primary)]">Profile</p>
                </x-mine.dropdown.item>

                <x-mine.dropdown.item>
                    <x-mine.icon name="cog-6-tooth" class="mr-3 text-[var(--mine-text-primary)]" />
                    <p class="text-sm text-[var(--mine-text-primary)]">Settings</p>
                </x-mine.dropdown.item>

                <x-mine.dropdown.divider class="px-2" />

                <x-mine.dropdown.item destructive x-data x-on:click.prevent="
                    fetch('{{ route('logout') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).then(() => window.Livewire?.navigate('{{ route('login') }}'))
                ">
                    <x-mine.icon name="arrow-right-start-on-rectangle" class="mr-3 text-[var(--mine-text-error)]" />
                    <p class="text-sm font-medium text-[var(--mine-text-error)]">Logout</p>
                </x-mine.dropdown.item>
            </x-mine.dropdown.content>
        </x-mine.dropdown>
    </div>
</div>
