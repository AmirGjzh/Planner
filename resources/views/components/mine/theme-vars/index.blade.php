@php
    $vars = config('themes.vars');
@endphp

@if ($vars)
<style>
    :root {
@foreach ($vars as $key => $value)
        --{{ $key }}: {{ $value }};
@endforeach
    }
</style>
@endif
