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
    tabindex="0"
    @click="close()"
    {{ $attributes->class([
        'flex w-full items-center rounded-lg transition-all duration-200 ease-out outline-none no-underline text-sm mine-text-primary',
        'hover:bg-(--mine-dropdown-item-bg-hover) focus:bg-(--mine-dropdown-item-bg-hover) focus-visible:ring-4 focus-visible:ring-(--mine-dropdown-item-ring)'
            => ! $destructive,
        'hover:bg-(--mine-dropdown-destructive-item-bg-hover) focus:bg-(--mine-dropdown-destructive-item-bg-hover) focus-visible:ring-4 focus-visible:ring-(--mine-dropdown-destructive-item-ring) text-(--mine-dropdown-item-text-danger)'
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
    tabindex="0"
    @disabled($disabled)
    @keydown.enter.prevent="$el.firstElementChild?.dispatchEvent(new MouseEvent('click', { bubbles: true }))"
    @keydown.space.prevent="$el.firstElementChild?.dispatchEvent(new MouseEvent('click', { bubbles: true }))"
    @click="close()"
    {{ $attributes->class([
        'flex w-full items-center rounded-lg transition-all duration-200 ease-out outline-none text-sm mine-text-primary',
        'hover:bg-(--mine-dropdown-item-bg-hover) focus:bg-(--mine-dropdown-item-bg-hover) focus-visible:ring-4 focus-visible:ring-(--mine-dropdown-item-ring)'
            => ! $destructive,
        'hover:bg-(--mine-dropdown-destructive-item-bg-hover) focus:bg-(--mine-dropdown-destructive-item-bg-hover) focus-visible:ring-4 focus-visible:ring-(--mine-dropdown-destructive-item-ring) text-(--mine-dropdown-item-text-danger)'
            => $destructive,
        'cursor-not-allowed opacity-50'
            => $disabled,
    ]) }}
>
    {{ $slot }}
</button>
@endif
