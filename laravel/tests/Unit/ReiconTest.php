<?php

use App\Support\Reicon;
use Illuminate\Support\Facades\Blade;

function reiconTestIcons(): array
{
    return json_decode(
        (string) file_get_contents(app_path('Support/reicon-icons.json')),
        true,
    ) ?? [];
}

it('renders an outline svg for a known icon', function () {
    $outlineName = array_key_first(array_filter(
        reiconTestIcons(),
        fn (array $data) => isset($data['O']),
    )) ?? null;

    if ($outlineName === null) {
        $this->markTestSkipped('No outline icon is exported.');
    }

    $svg = Reicon::svg($outlineName);

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
    $filledName = array_key_first(array_filter(
        reiconTestIcons(),
        fn (array $data) => isset($data['F']),
    )) ?? null;

    if ($filledName === null) {
        $this->markTestSkipped('No filled icon is exported.');
    }

    expect(Reicon::svg($filledName, 'filled'))->not->toBeNull();
});

it('returns null for an unknown icon', function () {
    expect(Reicon::svg('Bogus'))->toBeNull();
});

it('returns null for a null name', function () {
    expect(Reicon::svg(null))->toBeNull();
});

it('applies size, class and extra attributes', function () {
    $outlineName = array_key_first(array_filter(
        reiconTestIcons(),
        fn (array $data) => isset($data['O']),
    )) ?? null;

    if ($outlineName === null) {
        $this->markTestSkipped('No outline icon is exported.');
    }

    $svg = Reicon::svg($outlineName, 'outline', 20, 'size-4 text-red-500', ['x-cloak' => '']);

    expect($svg)
        ->toContain('width="20" height="20"')
        ->toContain('class="reicon size-4 text-red-500"')
        ->toContain('x-cloak=""');
});

it('renders an explicit size prop at its exact pixel value without a size class', function () {
    $anyName = array_key_first(reiconTestIcons()) ?? null;

    if ($anyName === null) {
        $this->markTestSkipped('No icon is exported.');
    }

    $svg = Blade::render('<x-mine.icon name="'.e($anyName).'" size="32" />');

    expect($svg)
        ->toContain('width="32" height="32"')
        ->not->toContain('size-');
});
