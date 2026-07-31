@props([
    'name' => null,
    'variant' => 'outline',
])

@php
    $component = match ($variant) {
        'solid' => "heroicons::solid.$name",
        'mini' => "heroicons::mini.solid.$name",
        'micro' => "heroicons::micro.solid.$name",
        default => "heroicons::outline.$name",
    };
    $hasSize = str($attributes->get('class'))->contains(['size-', 'w-', 'h-']);
@endphp

<x-dynamic-component
    :component="$component"
    {{ $attributes->class(['size-5' => !$hasSize]) }}
    data-slot="icon"
/>
