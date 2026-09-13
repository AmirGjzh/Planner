<?php

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
