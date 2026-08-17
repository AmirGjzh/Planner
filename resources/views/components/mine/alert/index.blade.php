@props([
    'variant' => 'danger',
    'title' => null,
])

@php
    $variants = [
        'danger' => [
            'container' => 'border-[var(--mine-alert-danger-border)] bg-[var(--mine-alert-danger-bg)]',
            'icon' => 'x-circle',
            'icon-color' => 'text-[var(--mine-alert-danger-icon)]',
            'title' => 'text-[var(--mine-alert-danger-title)]',
        ],
        'success' => [
            'container' => 'border-[var(--mine-alert-success-border)] bg-[var(--mine-alert-success-bg)]',
            'icon' => 'check-circle',
            'icon-color' => 'text-[var(--mine-alert-success-icon)]',
            'title' => 'text-[var(--mine-alert-success-title)]',
        ],
        'warning' => [
            'container' => 'border-[var(--mine-alert-warning-border)] bg-[var(--mine-alert-warning-bg)]',
            'icon' => 'exclamation-triangle',
            'icon-color' => 'text-[var(--mine-alert-warning-icon)]',
            'title' => 'text-[var(--mine-alert-warning-title)]',
        ],
        'info' => [
            'container' => 'border-[var(--mine-alert-info-border)] bg-[var(--mine-alert-info-bg)]',
            'icon' => 'information-circle',
            'icon-color' => 'text-[var(--mine-alert-info-icon)]',
            'title' => 'text-[var(--mine-alert-info-title)]',
        ],
    ];

    $style = $variants[$variant];
@endphp

<div
    {{ $attributes->class([
        'flex gap-3 rounded-xl border-2 p-4',
        $style['container'],
    ]) }}
>
    <div class="{{ $style['icon-color'] }}">
        <x-mine.icon name="{{ $style['icon'] }}" class="size-6" />
    </div>
    <div class="pt-0.5">
        @if($title)
            <h3 class="text-sm font-bold {{ $style['title'] }}">
                {{ $title }}
            </h3>
        @endif
        <div class="mt-1 font-medium text-sm mine-text-secondary">
            {{ $slot }}
        </div>
    </div>
</div>
