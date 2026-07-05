@aware([
    'striped' => false,
    'animated' => false,
    'color' => null,
    'wave' => false
])

@php
    
    $classes = [
        "h-full [:where(&)]:bg-(--color-primary) transition-[width] relative  overflow-hidden",
        // inderminate loading...
        'data-[indeterminate]:w-1/4 data-[indeterminate]:animate-[indeterminate_1.5s_ease-in-out_infinite]',
        // wave animations...
        'before:absolute before:inset-0 before:-translate-x-full' => $wave,
        'before:animate-[wave_2s_infinite_linear] before:bg-gradient-to-r before:from-transparent before:via-white/60 dark:before:via-white/50 before:to-transparent' => $wave,
    ];
@endphp

<div 
    {{ $attributes->class($classes) }}"
    data-slot="bar"
    x-bind:style="__indeterminate ? 'width: 100%' : `width: ${percentage}%;`" 
    x-cloak
    x-bind:data-indeterminate="__indeterminate"
></div>