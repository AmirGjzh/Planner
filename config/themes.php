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

$themes = ['ocean', 'forest', 'magic', 'safrron', 'amber', 'chocolate', 'gol-goli', 'midnight'];

$vars = [];

foreach ($themes as $theme) {
    $file = dirname(__DIR__).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$theme.'.json';
    $vars[$theme] = is_file($file) ? json_decode((string) file_get_contents($file), true) : [];
    unset($vars[$theme]['name']);
}

return [
    'name' => $themes,
    'vars' => $vars,
];
