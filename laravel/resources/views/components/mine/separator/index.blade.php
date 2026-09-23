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
            class="mx-2 flex w-6 flex-col items-center gap-2 self-stretch transition-colors duration-300"
            role="separator"
            aria-orientation="vertical"
            aria-label="{{ $label }}"
        >
            <div class="flex-1 w-px {{ $barClass }}" aria-hidden="true"></div>

            <span class="mine-text-secondary text-sm font-medium whitespace-nowrap select-none"> {{ $label }} </span>

            <div class="flex-1 w-px {{ $barClass }}" aria-hidden="true"></div>
        </div>
    @else
        <div
            class="mine-separator w-px self-stretch shrink-0 min-h-[1em] {{ $barClass }}"
            role="separator"
            aria-orientation="vertical"
        ></div>
    @endif

@elseif ($label)
    <div
        class="flex h-6 w-full items-center gap-4"
        transition-colors
        duration-200
        role="separator"
        aria-orientation="horizontal"
        aria-label="{{ $label }}"
    >
        <div class="flex-1 h-px {{ $barClass }}" aria-hidden="true"></div>

        <span class="mine-text-secondary text-sm font-medium whitespace-nowrap select-none"> {{ $label }} </span>

        <div class="flex-1 h-px {{ $barClass }}" aria-hidden="true"></div>
    </div>

@else
    <div class="mine-separator w-full h-px {{ $barClass }}" role="separator" aria-orientation="horizontal"></div>

@endif
