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
    {{
        $attributes->merge([
            'class' => '
                text-sm font-medium text-[var(--mine-nav-link-text)]
                py-4
                px-4
                gap-2
                flex
                justify-center
                items-center
                h-10
                rounded-xl
                transition-all
                duration-200
            '
                . ($active
                    ? ' bg-[var(--mine-nav-link-bg-active)] text-[var(--mine-nav-link-text-active)] font-medium'
                    : ' hover:bg-[var(--mine-nav-link-bg-hover)]'
                )
        ])
    }}
>
    {{ $slot }}
</a>
