@php
    // Error views render outside the route-middleware pipeline: an unknown URL
    // never starts the session, and the failure behind a 500 may be the database
    // or session store itself — so auth resolution is guarded and always falls
    // back to the app defaults (default theme + app locale).
    try {
        $user = auth()->user();
    } catch (Throwable) {
        $user = null;
    }

    app()->setLocale($user?->locale ?? config('app.locale'));

    $status = $exception->getStatusCode();

    $copy = [
        403 => ['title' => 'You do not have access to this page'],
        404 => ['title' => 'Page not found', 'description' => 'The page you are looking for does not exist or may have been moved.'],
        419 => ['title' => 'Page expired', 'description' => 'The form timed out, go back and try again.'],
        429 => ['title' => 'Too many requests', 'description' => 'Take a short break and try again.'],
        500 => ['title' => 'Something went wrong', 'description' => 'An unexpected error occurred, please try again.'],
        503 => ['title' => 'Under maintenance', 'description' => 'We will be back soon.'],
    ];

    $page = $copy[$status] ?? ['title' => 'Something went wrong'];
    $title = __($page['title']);
    $description = isset($page['description']) ? __($page['description']) : null;

    // Auth-aware target: guests (and unknown-session 404s) go to login, resolved
    // users straight to the dashboard — the `/` stub is never a landing spot.
    $homeUrl = $user ? route('dashboard') : route('login');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'fa' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>{{ $title }} — {{ config('app.name') }}</title>

    @vite(['resources/css/app.css'])
    <x-mine.theme-vars :theme="$user?->theme ?? config('themes.default')" />

    <style>
        /* The JS-free twin of x-mine.animate: the error shell ships without
           Alpine/Livewire, so the reveal can't rely on x-intersect — it degrades
           to a plain CSS animation, neutralized for reduced motion. */
        @keyframes mine-error-in {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
        }

        .mine-error-in {
            animation: mine-error-in 0.35s ease-out 100ms both;
        }

        @media (prefers-reduced-motion: reduce) {
            .mine-error-in {
                animation: none;
            }
        }
    </style>
</head>

<body class="mine-page-bg mine-text-primary flex min-h-dvh items-center justify-center p-6 font-sans">
    <main class="w-full max-w-md">
        <div class="mine-card mine-error-in p-8 text-center">
            <p aria-hidden="true" class="mine-text-secondary text-5xl font-semibold">
                {{ app()->isLocale('en') ? $status : \App\Support\PersianNumber::convert($status) }}
            </p>

            <h1 class="mt-6 text-lg font-semibold">{{ $title }}</h1>

            @if ($description)
                <p class="mine-text-secondary mt-2 text-sm leading-7 font-medium">{{ $description }}</p>
            @endif

            <a
                href="{{ $homeUrl }}"
                class="mine-btn-primary mt-6 inline-flex h-11 w-full cursor-pointer items-center justify-center rounded-xl px-4 text-base font-medium whitespace-nowrap select-none"
            >
                {{ __('Go to home') }}
            </a>
        </div>
    </main>
</body>
</html>
