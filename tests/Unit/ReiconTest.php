<?php

use App\Support\Reicon;
use Illuminate\Support\Facades\Blade;

it('renders an outline svg for a known icon', function () {
    $svg = Reicon::svg('Search');

    expect($svg)
        ->not->toBeNull()
        ->toContain('<svg')
        ->toContain('viewBox="0 0 24 24"')
        ->toContain('width="24" height="24"')
        ->toContain('class="reicon"')
        ->toContain('data-slot="icon"')
        ->toContain('</svg>');
});

it('renders the filled weight', function () {
    $svg = Reicon::svg('Menu', 'filled');

    expect($svg)
        ->not->toBeNull();
});

it('returns null for an unknown icon', function () {
    expect(Reicon::svg('Bogus'))->toBeNull();
});

it('returns null for a null name', function () {
    expect(Reicon::svg(null))->toBeNull();
});

it('applies size, class and extra attributes', function () {
    $svg = Reicon::svg('Check', 'outline', 20, 'size-4 text-red-500', ['x-cloak' => '']);

    expect($svg)
        ->toContain('width="20" height="20"')
        ->toContain('class="reicon size-4 text-red-500"')
        ->toContain('x-cloak=""');
});

it('renders an explicit size prop at its exact pixel value without a size class', function () {
    $svg = Blade::render('<x-mine.icon name="Search" size="32" />');

    expect($svg)
        ->toContain('width="32" height="32"')
        ->not->toContain('size-');
});
