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
        'flex w-full items-center rounded-lg px-2 py-2 transition-all duration-200 ease-out outline-none no-underline',
        'hover:bg-gray-100 focus:bg-[#ECF8F1] focus-visible:ring-4 focus-visible:ring-[#DDF3E7]'
            => ! $destructive,
        'hover:bg-[#FEF2F2] focus:bg-[#FDE8E8] focus-visible:ring-[#FAD4D4]'
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
        'flex w-full items-center rounded-lg px-2 py-2 transition-all duration-200 ease-out outline-none',
        'hover:bg-gray-100 focus:bg-[#ECF8F1] focus-visible:ring-4 focus-visible:ring-[#DDF3E7]'
            => ! $destructive,
        'hover:bg-[#FEF2F2] focus:bg-[#FDE8E8] focus-visible:ring-[#FAD4D4]'
            => $destructive,
        'cursor-not-allowed opacity-50'
            => $disabled,
    ]) }}
>
    {{ $slot }}
</button>
@endif
