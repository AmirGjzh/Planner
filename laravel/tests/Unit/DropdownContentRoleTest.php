<?php

use Illuminate\Support\Facades\Blade;

it('renders a menu role by default and no role for plain popovers', function () {
    expect(Blade::render('<x-mine.dropdown.content />'))
        ->toContain('role="menu"');

    // Calendar-hosting panels pass role="" — a menu must own menuitem children
    // (axe aria-required-children), so the panel drops the role entirely.
    expect(Blade::render('<x-mine.dropdown.content role="" />'))
        ->not->toContain('role=');
});
