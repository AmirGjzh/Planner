@php
    $vars = config('themes.vars')[auth()->user()?->theme ?? env('APP_THEME', 'forest')];
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
