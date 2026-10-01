<?php

use Illuminate\Support\Facades\Blade;

it('renders a labeled separator without dead attributes', function () {
    $html = Blade::render('<x-mine.separator label="OR" />');

    expect($html)
        ->toContain('role="separator"')
        ->not->toContain('transition-colors')
        ->not->toContain('duration-200');
});
