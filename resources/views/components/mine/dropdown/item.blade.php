@props([
    'href' => null,
    'route' => null,
    'disabled' => false,
    'destructive' => false,
    'value' => null,
    'active' => false,
])

@aware(['multiple' => false])

@php
    $href = $route ? route($route) : $href;
    $active = $active || ($route && request()->routeIs($route));
@endphp

@if ($href)
<a
    href="{{ $href }}"
    wire:navigate.hover
    role="menuitem"
    tabindex="0"
    @click="close()"
    {{ $attributes->class([
        'group flex w-full items-center rounded-lg transition-all duration-200 ease-out outline-none no-underline text-sm mine-text-secondary',
        'hover:bg-(--mine-dropdown-item-bg-hover) hover:text-(--mine-text-link) focus:bg-(--mine-dropdown-item-bg-hover) focus:text-(--mine-text-link) focus-visible:ring-4 focus-visible:ring-(--mine-dropdown-item-ring)'
            => ! $destructive && ! $active,
        'bg-(--mine-dropdown-item-bg-hover) text-(--mine-text-link) hover:bg-(--mine-dropdown-item-bg-hover) hover:text-(--mine-text-link) focus:bg-(--mine-dropdown-item-bg-hover) focus:text-(--mine-text-link) focus-visible:ring-4 focus-visible:ring-(--mine-dropdown-item-ring)'
            => ! $destructive && $active,
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
    role="{{ $multiple ? 'menuitemcheckbox' : 'menuitem' }}"
    tabindex="0"
    @disabled($disabled)
    @if ($multiple)
        :aria-checked="isSelected(@js($value))"
    @endif
    @keydown.enter.prevent="$el.firstElementChild?.dispatchEvent(new MouseEvent('click', { bubbles: true }))"
    @keydown.space.prevent="$el.firstElementChild?.dispatchEvent(new MouseEvent('click', { bubbles: true }))"
    @click="multiple ? toggleSelect(@js($value)) : close()"
{{ $attributes->class([
        'group flex w-full items-center rounded-lg transition-all duration-200 ease-out outline-none text-sm mine-text-secondary',
        'hover:bg-(--mine-dropdown-item-bg-hover) hover:text-(--mine-text-link) focus:bg-(--mine-dropdown-item-bg-hover) focus:text-(--mine-text-link) focus-visible:ring-4 focus-visible:ring-(--mine-dropdown-item-ring)'
            => ! $destructive && ! $active,
        'bg-(--mine-dropdown-item-bg-hover) text-(--mine-text-link) hover:bg-(--mine-dropdown-item-bg-hover) hover:text-(--mine-text-link) focus:bg-(--mine-dropdown-item-bg-hover) focus:text-(--mine-text-link) focus-visible:ring-4 focus-visible:ring-(--mine-dropdown-item-ring)'
            => ! $destructive && $active,
        'hover:bg-(--mine-dropdown-destructive-item-bg-hover) focus:bg-(--mine-dropdown-destructive-item-bg-hover) focus-visible:ring-4 focus-visible:ring-(--mine-dropdown-destructive-item-ring) text-(--mine-dropdown-item-text-danger)'
            => $destructive,
        'cursor-not-allowed opacity-50'
            => $disabled,
    ]) }}
>
    @if ($multiple)
    <div @class([
        "pl-2" => app()->isLocale('en'),
        "pr-2" => app()->isLocale('fa'),
        "cursor-pointer"
    ])>
        <span
            class="shrink-0 inline-flex items-center justify-center size-5 rounded-[6px] border-2 transition-all duration-200 ease-out"
            :class="isSelected(@js($value)) ? 'bg-(--mine-checkbox-bg) border-(--mine-checkbox-bg) text-(--mine-checkbox-text)' : 'border-(--mine-input-border) text-transparent'"
            aria-hidden="true"
        >
            <x-mine.icon name="Check" weight="filled" size="12" class="transition-all duration-200 ease-out" />
        </span>
    </div>
    @endif
    {{ $slot }}
</button>
@endif
