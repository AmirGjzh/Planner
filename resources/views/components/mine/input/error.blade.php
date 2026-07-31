@props([
    'name',
])

@error($name)
    <p {{ $attributes->class([
        'mt-2 text-sm font-medium mine-text-error',
    ]) }}>
        {{ $message }}
    </p>
@enderror
