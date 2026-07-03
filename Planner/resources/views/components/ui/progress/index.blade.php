@props([
    'value' => 0,
    'max' => 100,
    'min' => 0,
    'buffer' => null,
    'size' => 'md',
    'duration' => 300,
    'top' => null,
    'bottom' => null,
    'wave' => false
])
<div 
    x-data="progressComponent({
        min: @js($min),
        max: @js($max),
        duration: @js($duration),
        value: @js($value),
        buffer: @js($buffer),
    })"
    {{ $attributes->class(['relative w-full']) }} 
    role="progressbar"
    x-modelable="__state"    
    x-bind:aria-valuenow="__indeterminate ? null : __value" 
    x-bind:data-indeterminate="__indeterminate" 
    x-bind:aria-valuemin="min" 
    x-bind:aria-valuemax="max"
    x-bind:aria-busy="__indeterminate"
    x-bind:aria-label="__indeterminate ? 'Loading' : `{{ $label ?? 'Progress' }}: ${displayValue}%`"
>

    @if ($top)
        <div>
            {{ $top }}
        </div>
    @endif

    <div 
        @class([
            'relative overflow-hidden rounded-full shadow-sm',
            'bg-neutral-200 dark:bg-neutral-800',
            match ($size) {
                'xs' => 'h-1',
                'sm' => 'h-1.5',
                'md' => 'h-2',
                'lg' => 'h-3',
                'xl' => 'h-4',
                default => 'h-2',
            },
        ])
    >
        {{-- Buffer bar --}}
        <div    
            x-show="bufferPercentage > 0 && !__indeterminate"
            x-cloak
            data-slot="buffer"
            class="absolute rounded-l-full inset-0 bg-neutral-300 dark:bg-neutral-700 transition-all"
            x-bind:style="`width: ${bufferPercentage}%; transition-duration: ${duration}ms`"
        >
        </div>

        <x-ui.progress.main-bar />
    </div>
    
    @if ($bottom)
        <div>
            {{ $bottom }}
        </div>
    @endif
</div>