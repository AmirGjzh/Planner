<?php

use App\Models\User;

function headerUser(): User
{
    return User::factory()->create([
        'username' => 'header_user',
        'email' => 'header@example.com',
    ]);
}

it('serves the header landmarks and a11y wiring on an authenticated page', function () {
    $this->actingAs(headerUser());

    $this->get(route('profile'))
        ->assertStatus(200)
        ->assertSee('<header', false)
        ->assertSee('aria-label="Main navigation"', false)
        ->assertSee('aria-label="Menu"', false)
        ->assertSee('aria-haspopup="menu"', false)
        ->assertSee('href="#main-content"', false)
        ->assertSee('id="main-content"', false)
        ->assertDontSee('aria-current="page"', false);
});

it('marks the active nav link with aria-current', function () {
    $this->actingAs(headerUser());

    $this->get(route('tasks'))
        ->assertStatus(200)
        ->assertSee('aria-current="page"', false)
        ->assertSee('class="mine-nav-link-active"', false);
});

it('renders navigation links without hover prefetch', function () {
    $this->actingAs(headerUser());

    // Plain wire:navigate only: hover-prefetch would cache the destination
    // HTML for 30s and swap in a snapshot taken before a mutation landed on
    // another page (stale data until a manual refresh).
    $this->get(route('tasks'))
        ->assertStatus(200)
        ->assertSee('wire:navigate', false)
        ->assertDontSee('wire:navigate.hover', false);
});

it('renders exactly one h1 across header and page', function () {
    $this->actingAs(headerUser());

    $html = $this->get(route('profile'))
        ->assertStatus(200)
        ->getContent();

    expect(substr_count($html, '<h1'))->toBe(1);
});

it('keeps the profile route render at zero database queries', function () {
    $this->actingAs(headerUser());

    $this->expectsDatabaseQueryCount(0);

    $this->get(route('profile'))->assertStatus(200);
});
