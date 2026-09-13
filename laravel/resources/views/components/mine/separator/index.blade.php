@props([
    'label' => null,
    'vertical' => false,
    'variant' => 'primary',
])

@php
    $barClass = match ($variant) {
        'danger' => 'bg-(--mine-separator-danger-border)',
        'secondary' => 'bg-(--mine-separator-secondary-border)',
        default => 'bg-(--mine-separator-border)',
    };
@endphp

@if ($vertical)

    @if ($label)
        <div
            class="flex flex-col items-center self-stretch mx-2 gap-2 w-6 transition-colors duration-300"
            role="separator"
            aria-orientation="vertical"
            aria-label="{{ $label }}"
        >
            <div class="flex-1 w-px {{ $barClass }}" aria-hidden="true"></div>

            <span class="text-sm font-medium mine-text-secondary whitespace-nowrap select-none">
                {{ $label }}
            </span>

            <div class="flex-1 w-px {{ $barClass }}" aria-hidden="true"></div>
        </div>
    @else
        <div
            class="mine-separator w-px self-stretch shrink-0 min-h-[1em] {{ $barClass }}"
            role="separator"
            aria-orientation="vertical"
        ></div>
    @endif

@elseif($label)

<div
    class="flex items-center w-full gap-4 h-6" transition-colors duration-200
    role="separator"
    aria-orientation="horizontal"
    aria-label="{{ $label }}"
>
    <div class="flex-1 h-px {{ $barClass }}" aria-hidden="true"></div>

    <span class="text-sm font-medium mine-text-secondary whitespace-nowrap select-none">
        {{ $label }}
    </span>

    <div class="flex-1 h-px {{ $barClass }}" aria-hidden="true"></div>
</div>

@else

<div
    class="mine-separator w-full h-px {{ $barClass }}"
    role="separator"
    aria-orientation="horizontal"
></div>

@endif
