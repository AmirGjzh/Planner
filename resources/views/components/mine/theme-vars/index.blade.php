@php
    $vars = config('themes.vars');
    $darkVars = config('themes.dark_vars');
@endphp

@if ($vars)
<style>
    :root {
@foreach ($vars as $key => $value)
        --{{ $key }}: {{ $value }};
@endforeach
    }
@if ($darkVars && $darkVars !== $vars)
    .dark {
@foreach ($darkVars as $key => $value)
        --{{ $key }}: {{ $value }};
@endforeach
    }
@endif
</style>
@endif
