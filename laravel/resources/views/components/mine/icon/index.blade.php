@props([
    'name' => null,
    'weight' => 'outline',
    'size' => 24,
])

{!!
    \App\Support\Reicon::svg(
        $name,
        $weight,
        $size,
        trim((string) $attributes->get('class')),
        $attributes->except('class')->getAttributes(),
    )
!!}
