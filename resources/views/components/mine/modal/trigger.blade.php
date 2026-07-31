@props([
    'id' => null,
])

<div
    x-data
    @click="$dispatch('open-modal', { id: @js($id) })"
    {{ $attributes->merge(['class' => 'inline-block cursor-pointer']) }}
>
    {{ $slot }}
</div>
