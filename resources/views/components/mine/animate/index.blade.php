@props([
    'as' => 'div',
    'delay' => null,
    'duration' => null,
    'stagger' => null,
])

@php
    $delayVal = $delay ? (int) $delay : 0;
    $durationVal = $duration ? (int) $duration : 500;
    $staggerVal = $stagger ? (int) $stagger : 0;
@endphp

<{{ $as }}
    x-data="{
        init() {
            const d = {{ $staggerVal }} ? ({{ $staggerVal }} * (this.$el.dataset.animIndex || 0)) + {{ $delayVal }} : {{ $delayVal }};
            this.$el.style.transitionDelay = d + 'ms';
            this.$el.style.transitionDuration = '{{ $durationVal }}ms';
        }
    }"
    x-intersect.once.margin.50px="$el.classList.add('mine-animate-visible')"
    {{ $attributes->merge(['class' => 'mine-animate']) }}
>
    {{ $slot }}
</{{ $as }}>
