@props([])

<hr
    role="separator"
    {{
        $attributes->merge([
            'class' => '
        my-1
        border-2
        border-t
        border-(--mine-separator-border)
        opacity-20
        ',
        ])
    }}
/>
