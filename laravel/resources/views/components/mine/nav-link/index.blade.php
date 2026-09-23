@props([
    'href' => '#',
    'route' => null,
    'active' => false,
])

@php
    $active = $active || ($route && request()->routeIs($route));
@endphp

<a
    wire:navigate.hover
    href="{{ $href }}"
    class="{{ $active ? 'mine-nav-link-active' : 'mine-nav-link' }}"
    {{ $active ? 'aria-current="page"' : '' }}
>
    <p @class([
        'pt-1' => app()->isLocale('en'),
        'pt-0.5' => app()->isLocale('fa'),
        'text-sm font-medium',
    ])>
        {{ $slot }}
    </p>
</a>
