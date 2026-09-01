<?php

it('defaults to the forest theme when APP_THEME is unset', function () {
    expect(config('themes.name'))->toBe('forest');
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

it('exposes a dark_vars set for every available theme', function () {
    foreach (config('themes.available') as $theme) {
        $path = base_path("config/themes/{$theme}-dark.json");
        $darkVars = json_decode((string) file_get_contents($path), true);

        expect($darkVars)->toBeArray();
        expect($darkVars['name'] ?? null)->toBe($theme.'-dark');

        unset($darkVars['name']);

        $light = json_decode(
            (string) file_get_contents(base_path("config/themes/{$theme}.json")),
            true
        );
        unset($light['name']);

        expect($darkVars)->not->toBeEmpty();
        expect(array_keys($darkVars))->toHaveCount(count($light) + 2);
    }
});
