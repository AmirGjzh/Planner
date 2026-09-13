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
