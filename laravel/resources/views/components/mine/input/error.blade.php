@props([
    'name',
])

@error($name)
    <p
        id="{{ $name }}-error"
        role="alert"
        {{
            $attributes->class([
                'mt-2 text-sm font-medium mine-text-error',
            ])
        }}
    >
        {{ $message }}
    </p>
@enderror
