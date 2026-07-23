@props([
    'href' => null,
    'route' => null,
    'disabled' => false,
    'destructive' => false,
])

@php $href = $route ? route($route) : $href; @endphp

@if ($href)
<a
    href="{{ $href }}"
    wire:navigate.hover
    role="menuitem"
    tabindex="-1"
    @click="close()"
    {{ $attributes->class([
        'flex w-full items-center rounded-lg transition-all duration-200 ease-out outline-none no-underline text-sm mine-text-primary',
        'hover:bg-(--mine-btn-ghost-bg-hover) focus:bg-(--mine-btn-ghost-bg-hover) focus-visible:ring-4 focus-visible:ring-(--mine-input-ring-focus)'
            => ! $destructive,
        'hover:bg-(--mine-alert-danger-icon-bg) focus:bg-(--mine-alert-danger-icon-bg) focus-visible:ring-4 focus-visible:ring-(--mine-alert-danger-border)'
            => $destructive,
        'cursor-not-allowed opacity-50'
            => $disabled,
    ]) }}
>
    {{ $slot }}
</a>
@else
<button
    type="button"
    role="menuitem"
    tabindex="-1"
    @disabled($disabled)
    @click="close()"
    {{ $attributes->class([
        'flex w-full items-center rounded-lg transition-all duration-200 ease-out outline-none text-sm mine-text-primary',
        'hover:bg-(--mine-btn-ghost-bg-hover) focus:bg-(--mine-btn-ghost-bg-hover) focus-visible:ring-4 focus-visible:ring-(--mine-input-ring-focus)'
            => ! $destructive,
        'hover:bg-(--mine-alert-danger-icon-bg) focus:bg-(--mine-alert-danger-icon-bg) focus-visible:ring-4 focus-visible:ring-(--mine-alert-danger-border)'
            => $destructive,
        'cursor-not-allowed opacity-50'
            => $disabled,
    ]) }}
>
    {{ $slot }}
</button>
@endif
