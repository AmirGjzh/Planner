@props([
    'theme' => null,
])

@php
    $vars = config('themes.vars')[$theme ?? auth()->user()?->theme ?? config('themes.default')];
    $colorScheme = $vars['color-scheme'] ?? 'light';
    unset($vars['color-scheme']);
@endphp

@if ($vars)
    <style>
        :root {
            color-scheme: {{ $colorScheme }};
            @foreach ($vars as $key => $value)
                --{{ $key }}: {{ $value }};
            @endforeach
        }
    </style>
@endif
