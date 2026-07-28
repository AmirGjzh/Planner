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
>
    {{ $slot }}
</a>
