@php
    $classes = [
        '[grid-area:main]',
        'overflow-y-auto',
        'h-full',
        '[&>:has([data-slot=header])]:p-0 [&>:not(:has([data-slot=header]))]:p-2'
    ];
@endphp

<div
    {{ $attributes->class($classes) }}
    data-slot="main"
>
    {{ $slot }}
</div>
