<?php

use Illuminate\Support\Facades\Blade;

it('renders the logo inline so the active theme color applies', function () {
    $html = Blade::render('<x-mine.brand-logo />');

    expect($html)
        ->toContain('<svg')
        ->toContain('viewBox="0 0 1419 281"')
        ->toContain('class="mine-logo"')
        ->not->toContain('<img');
});

it('never hardcodes the brand color', function () {
    $html = Blade::render('<x-mine.brand-logo />');

    expect($html)
        ->toContain('fill="currentColor"')
        ->not->toContain('#173B67');
});

it('labels the logo for assistive technology', function () {
    $html = Blade::render('<x-mine.brand-logo />');

    expect($html)
        ->toContain('role="img"')
        ->toContain('aria-label="'.config('app.name').'"');
});

it('lets callers override the logo label', function () {
    $html = Blade::render('<x-mine.brand-logo aria-label="Custom logo" />');

    expect($html)
        ->toContain('aria-label="Custom logo"')
        ->and(substr_count($html, 'aria-label'))->toBe(1);
});
