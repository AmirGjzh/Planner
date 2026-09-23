@props([
    'placement' => 'bottom-end',
])

@php
    $placements = [
        'bottom-start' => 'left-0 top-full mt-0 origin-top-left',
        'bottom-end' => 'right-0 top-full mt-0 origin-top-right',
        'top-start' => 'left-0 bottom-full mb-0 origin-bottom-left',
        'top-end' => 'right-0 bottom-full mb-0 origin-bottom-right',
    ];
    $position = $placements[$placement] ?? $placements['bottom-end'];
@endphp

<div
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    x-cloak
    @mouseenter="cancelHide()"
    @mouseleave="hide(150)"
    @keydown.down.prevent="focusNext()"
    @keydown.up.prevent="focusPrev()"
    @keydown.home.prevent="focusFirst()"
    @keydown.end.prevent="focusLast()"
    {{
        $attributes->merge([
            'class' => '
        absolute
        z-50
        min-w-full w-max '.
            $position.
            ' bg-(--mine-dropdown-bg)
        rounded-xl
        border-2
        border-(--mine-dropdown-border)
        shadow-lg
        p-1
        ',
        ])
    }}
>
    {{ $slot }}
</div>
