@props([
    'theme' => null,
])

@php
    // Single theming point: the CSS variables, the browser-chrome color, and the
    // favicon must all follow the same resolved theme — including the auth
    // layout's `:theme` pin — so all three render from this one component.
    $themeSlug = $theme ?? auth()->user()?->theme ?? config('themes.default');
    $favicon = "favicons/{$themeSlug}.svg";
    if (! is_file(public_path($favicon))) {
        $favicon = 'favicon.svg';
    }

    $vars = config('themes.vars')[$themeSlug];
    $colorScheme = $vars['color-scheme'] ?? 'light';
    unset($vars['color-scheme']);
@endphp

<link rel="icon" type="image/svg+xml" href="{{ asset($favicon) }}" />

@if ($vars)
    @isset($vars['mine-page-bg'])
        <meta name="theme-color" content="{{ $vars['mine-page-bg'] }}" />
    @endisset
    <style>
        :root {
            color-scheme: {{ $colorScheme }};
            @foreach ($vars as $key => $value)
                --{{ $key }}: {{ $value }};
            @endforeach
        }
    </style>
@endif
