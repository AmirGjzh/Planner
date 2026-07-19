@props([])

<hr
    role="separator"
    {{ $attributes->merge([
        'class' => '
            my-2
            border-1.5
            border-t
            border-(--mine-separator-border)
        ',
    ]) }}
>
