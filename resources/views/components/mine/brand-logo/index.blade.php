@props([
    'width' => 100,
])

@php
    $paths = '';

    $file = public_path('storage/images/logo.svg');

    if (is_file($file)) {
        $svg = simplexml_load_file($file);

        if ($svg !== false) {
            foreach ($svg->path as $path) {
                $path['fill'] = 'currentColor';
            }

            $paths = $svg->path->asXML();
        }
    }
@endphp

@if($paths)
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 1419 281"
        width="{{ $width }}"
        role="img"
        {{ $attributes->class(['mine-logo']) }}
    >{!! $paths !!}</svg>
@endif