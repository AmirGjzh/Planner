@php
    $githubUrl = 'https://github.com/amirgjzh/planner';
    $user = auth()->user();

    // The copyright year follows the calendar of the UI language: Jalali (Persian
    // digits via the fa_IR formatter) for fa, Gregorian otherwise.
    $copyrightYear = app()->isLocale('fa')
        ? \App\Support\Jalali::format(now(), 'yyyy')
        : date('Y');

    $navLinks = [
        ['label' => __('Dashboard'), 'route' => 'dashboard'],
        ['label' => __('Tasks'), 'route' => 'tasks'],
        ['label' => __('Plans'), 'route' => 'plans'],
        ['label' => __('Categories'), 'route' => 'categories'],
        ['label' => __('Reports'), 'route' => 'reports'],
    ];

    // Component-tag attributes are extracted as raw strings, so @js would stay
    // literal there — build the Alpine data in PHP and pass it with :x-data (header parity).
    $logoutData = 'mineLogout('.\Illuminate\Support\Js::from([
        'action' => route('logout'),
        'home' => route('home'),
        'csrf' => csrf_token(),
        'failed' => __('Log out failed'),
    ]).')';
@endphp

{{-- The contentinfo landmark: themed like the header (`mine-card m-2`), logical utilities only — direction comes from the `<html dir>`. The middle renders in every state; the Settings column carries the auth actions: Account settings + Log out for a signed-in visitor, Sign in / Sign up for a guest. --}}
<footer class="mine-card mx-auto my-2 w-[calc(100%_-_1rem)] max-w-7xl">
    <div class="mx-auto px-8 py-10">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 md:grid-cols-3">
            {{-- Brand --}}
            <div class="text-center sm:text-start">
                <a wire:navigate href="{{ route('home') }}" class="inline-block">
                    <x-mine.brand-logo width="100" aria-label="{{ config('app.name') }}" />
                </a>

                <p class="mine-text-secondary mt-2 text-sm leading-7 font-medium">
                    {{ __('Plan better, live orderly') }}
                </p>
            </div>

            {{-- Quick links + settings, in every state --}}
            <div class="flex justify-around md:justify-between">
                <nav aria-label="{{ __('Footer navigation') }}" class="flex flex-col items-center sm:items-start">
                    <h2 class="mine-text-primary mb-4 text-[15px] font-semibold">{{ __('Menu') }}</h2>

                    <ul class="flex flex-col items-center space-y-3 text-[14px] sm:items-start">
                        @foreach ($navLinks as $link)
                            <li>
                                <a
                                    wire:navigate
                                    href="{{ route($link['route']) }}"
                                    @if (request()->routeIs($link['route'])) aria-current="page" @endif
                                    class="mine-text-link inline-block font-medium"
                                >
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>

                <div class="flex flex-col items-center sm:items-start">
                    <h2 class="mine-text-primary mb-4 text-[15px] font-semibold">{{ __('Settings') }}</h2>

                    <ul class="flex flex-col items-center space-y-3 text-[14px] sm:items-start">
                        @if ($user)
                            <li>
                                <a
                                    wire:navigate
                                    href="{{ route('profile') }}"
                                    class="mine-text-link inline-block font-medium"
                                >
                                    {{ __('Profile') }}
                                </a>
                            </li>
                            <li>
                                <button
                                    type="button"
                                    x-data="{{ $logoutData }}"
                                    x-on:click.prevent="logout()"
                                    class="mine-text-error inline-block rounded-sm font-medium hover:cursor-pointer focus-visible:ring-2 focus-visible:ring-(--mine-text-link-ring-focus) focus-visible:outline-none"
                                >
                                    {{ __('Log out') }}
                                </button>
                            </li>
                        @else
                            <li>
                                <a
                                    wire:navigate
                                    href="{{ route('login') }}"
                                    class="mine-text-link inline-block font-medium"
                                >
                                    {{ __('Sign in') }}
                                </a>
                            </li>
                            <li>
                                <a
                                    wire:navigate
                                    href="{{ route('register') }}"
                                    class="mine-text-link inline-block font-medium"
                                >
                                    {{ __('Sign up') }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            {{-- Resources --}}
            <div class="flex flex-col items-center sm:relative sm:-top-30 sm:items-start md:top-0 md:items-end">
                <a
                    href="{{ $githubUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="group mine-card inline-flex items-start gap-4 rounded-lg px-12 py-4 shadow-none transition-colors hover:bg-(--mine-dropdown-item-bg-hover) sm:px-8 md:px-7"
                >
                    <svg
                        class="mt-0.5 size-8 shrink-0 text-(--mine-btn-primary-bg)"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path d="M12 .9a11.1 11.1 0 0 0-3.51 21.63c.56.1.76-.24.76-.54v-2.1c-3.1.68-3.75-1.32-3.75-1.32-.51-1.3-1.25-1.65-1.25-1.65-1.02-.7.08-.69.08-.69 1.13.08 1.73 1.16 1.73 1.16 1 1.72 2.62 1.22 3.26.93.1-.73.39-1.22.71-1.5-2.47-.28-5.07-1.24-5.07-5.5 0-1.22.44-2.22 1.16-3-.12-.29-.5-1.43.11-2.98 0 0 .95-.3 3.05 1.15a10.6 10.6 0 0 1 5.55 0c2.1-1.45 3.04-1.15 3.04-1.15.61 1.55.23 2.69.12 2.98.72.78 1.15 1.78 1.15 3 0 4.27-2.6 5.22-5.08 5.5.4.34.75 1.02.75 2.06v3.11c0 .3.2.65.77.54A11.1 11.1 0 0 0 12 .9Z" />
                    </svg>

                    <span>
                        <span class="mine-text-primary group-hover:mine-text-link block text-[15px] font-semibold">
                            GitHub
                        </span>
                        <span class="mine-text-secondary mt-1 block text-[13px] leading-5 font-medium">
                            {{ __('View the project and contribute') }}
                        </span>
                    </span>
                </a>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="mt-6 -mb-4 flex flex-col items-center justify-between gap-2 border-t-2 border-(--mine-separator-border) pt-6 text-center text-xs font-medium sm:-mt-22 sm:-mb-4 sm:flex-row sm:text-start md:mt-6 md:-mb-4">
            <p class="mine-text-secondary text-sm">
                <span class="mine-text-secondary text-base">©</span>
                {{ __(':app :year - All rights reserved', ['year' => $copyrightYear, 'app' => config('app.name')]) }}
            </p>

            <p class="mine-text-secondary text-[13px]">
                {{ __('Made with') }}
                <span aria-hidden="true" class="text-lg text-(--mine-btn-danger-bg)">♥</span>
                <span class="sr-only">{{ __('love') }}</span>
                {{ __('for a better future') }}
            </p>
        </div>
    </div>
</footer>
