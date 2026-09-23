@props([
    'total' => 100,
    'progress' => 0,
    'variant' => 'primary',
])

@php
    $percent = min(100, max(0, $total > 0 ? ($progress / $total) * 100 : 0));
    $trackVar = match ($variant) {
        'gray' => '--mine-progress-gray-track',
        'danger' => '--mine-progress-danger-track',
        default => '--mine-progress-track',
    };
    $fillVar = match ($variant) {
        'gray' => '--mine-progress-gray-fill',
        'danger' => '--mine-progress-danger-fill',
        default => '--mine-progress-fill',
    };
@endphp

<div
    {{ $attributes->class('w-full rounded-full h-2') }}
    style="background: var({{ $trackVar }});"
    role="progressbar"
    aria-valuenow="{{ $progress }}"
    aria-valuemin="0"
    aria-valuemax="{{ $total }}"
>
    <div
        class="rounded-full transition-all duration-500 ease-out"
        style="width: {{ $percent }}%; height: 100%; background: var({{ $fillVar }});"
    ></div>
</div>
