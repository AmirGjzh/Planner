@props([
    'for' => null,
    'required' => false,
])

<label
    @if($for)
        for="{{ $for }}"
    @endif

    {{ $attributes->class([
        'mb-2 block text-sm font-medium mine-text-primary',
    ]) }}
>
    {{ $slot }}

    @if($required)
        <span class="mine-text-error">*</span>
    @endif
</label>
