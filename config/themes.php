<?php

/*
|--------------------------------------------------------------------------
| Theme Configuration
|--------------------------------------------------------------------------
|
| The active UI theme is chosen via the APP_THEME environment variable.
| Each theme lives in config/themes/{theme}.json and defines the full set
| of --mine-* CSS custom properties consumed by resources/css/mine.css.
|
*/

$themes = ['ocean', 'forest', 'magic', 'safrron', 'amber'];

$theme = env('APP_THEME', 'blue');

if (! in_array($theme, $themes, true)) {
    $theme = 'blue';
}

// config/themes/{theme}.json lives one level above the config directory.
$file = dirname(__DIR__).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$theme.'.json';

$vars = is_file($file) ? json_decode((string) file_get_contents($file), true) : [];

if (! is_array($vars)) {
    $vars = [];
}

unset($vars['name']);

return [
    'name' => $theme,
    'available' => $themes,
    'vars' => $vars,
];
