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
        revealed: false,
        init() {
            const el = this.$el;
            const d = {{ $staggerVal }} ? ({{ $staggerVal }} * (this.$el.dataset.animIndex || 0)) + {{ $delayVal }} : {{ $delayVal }};
            el.style.transitionDelay = d + 'ms';
            el.style.transitionDuration = '{{ $durationVal }}ms';

            new MutationObserver(() => {
                if (this.revealed && !el.classList.contains('mine-animate-visible')) {
                    el.classList.add('mine-animate-visible');
                }
            }).observe(el, { attributes: true, attributeFilter: ['class'] });
        }
    }"
    x-intersect.once.margin.50px="revealed = true; $el.classList.add('mine-animate-visible')"
    {{ $attributes->merge(['class' => 'mine-animate']) }}
>
    {{ $slot }}
</{{ $as }}>
