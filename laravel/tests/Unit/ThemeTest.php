<?php

use Illuminate\Support\Facades\Blade;

it('defaults to the forest theme when APP_THEME is unset', function () {
    $vars = json_decode(
        (string) file_get_contents(base_path('config/themes/forest.json')),
        true,
    );
    unset($vars['name']);
    config(['themes' => ['name' => 'forest', 'available' => config('themes.available'), 'vars' => $vars]]);

    expect(config('themes.name'))->toBe('forest');
    expect(config('themes.vars'))->toBeArray()->not->toBeEmpty();
});

it('bundles a valid, complete theme json for every available theme', function () {
    foreach (config('themes.name') as $theme) {
        $path = base_path("config/themes/{$theme}.json");
        $vars = json_decode((string) file_get_contents($path), true);

        expect($vars)->toBeArray();
        expect($vars['name'] ?? null)->toBe($theme);

        unset($vars['name']);

        expect($vars)->not->toBeEmpty();
    }
});

it('exposes the same variable set as each theme json', function () {
    foreach (config('themes.name') as $theme) {
        $json = json_decode(
            (string) file_get_contents(base_path("config/themes/{$theme}.json")),
            true
        );
        unset($json['name']);

        expect(config('themes.vars')[$theme])->toBe($json);
    }
});

it('emits the theme color-scheme inside :root without a custom property', function () {
    $html = Blade::render('<x-mine.theme-vars />');

    expect($html)
        ->toContain('color-scheme: light')
        ->not->toContain('--color-scheme');
});

it('reads the fallback theme from config so config:cache stays correct', function () {
    config(['themes.default' => 'magic']);

    $html = Blade::render('<x-mine.theme-vars />');

    $ocean = collect(config('themes.vars')['ocean'])->except('color-scheme');
    $magic = collect(config('themes.vars')['magic'])->except('color-scheme');
    $key = $ocean->keys()->first(fn ($name) => $magic->get($name) !== $ocean->get($name));

    expect($key)->not->toBeNull()
        ->and($html)->toContain('--'.$key.': '.$magic->get($key));

    expect(file_get_contents(base_path('resources/views/components/mine/theme-vars/index.blade.php')))
        ->not->toContain('env(');
});
