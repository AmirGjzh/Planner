<?php

it('defaults to the blue theme when APP_THEME is unset', function () {
    expect(config('themes.name'))->toBe('blue');
    expect(config('themes.vars'))->toBeArray()->not->toBeEmpty();
});

it('bundles a valid, complete theme json for every available theme', function () {
    foreach (config('themes.available') as $theme) {
        $path = base_path("config/themes/{$theme}.json");
        $vars = json_decode((string) file_get_contents($path), true);

        expect($vars)->toBeArray();
        expect($vars['name'] ?? null)->toBe($theme);

        unset($vars['name']);

        expect($vars)->not->toBeEmpty();
    }
});

it('exposes the same variable set as the active theme json', function () {
    $theme = config('themes.name');
    $json = json_decode(
        (string) file_get_contents(base_path("config/themes/{$theme}.json")),
        true
    );
    unset($json['name']);

    expect(config('themes.vars'))->toBe($json);
});
