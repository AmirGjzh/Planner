@props([
    'placement' => 'bottom-end',
])

@php
$placements = [
    'bottom-start' => 'left-0 top-full mt-2 origin-top-left',
    'bottom-end' => 'right-0 top-full mt-2 origin-top-right',
    'top-start' => 'left-0 bottom-full mb-2 origin-bottom-left',
    'top-end' => 'right-0 bottom-full mb-2 origin-bottom-right',
];
$position = $placements[$placement] ?? $placements['bottom-end'];
@endphp

<div
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    x-cloak
    @mouseenter="cancelHide()"
    @mouseleave="hide(150)"
    @keydown.down.prevent="focusNext()"
    @keydown.up.prevent="focusPrev()"
    @keydown.home.prevent="focusFirst()"
    @keydown.end.prevent="focusLast()"
    class="
        absolute
        z-50
        w-max max-w-80
        {{ $position }}
        rounded-xl
        border
        border-[#E4ECE7]
        bg-white
        shadow-md
        p-2
    "
    {{ $attributes }}
>
    {{ $slot }}
</div>
