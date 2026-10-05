<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('login:127.0.0.1');
});

it('keeps a toast alive across page changes and reloads until its deadline', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'password')
        ->click('Sign in')
        ->assertPathIs('/dashboard');

    // Text assertions snapshot the page once, so every step below sleeps
    // before asserting — Alpine renders toasts on the next frame.
    $page->wait(1)
        ->assertSee('You\'re signed in');

    // Probe toasts drive the navigation steps: a long-lived one so they never
    // race the default 4s flash toast's deadline, and a short-lived one to
    // prove the deadline still holds after a restore.
    $page->script("window.dispatchEvent(new CustomEvent('toast', { detail: { title: 'Persistence probe', duration: 60000 } }))");
    $page->wait(1)
        ->assertSee('Persistence probe');

    // An in-app (wire:navigate) page change keeps the toast on screen.
    $page->click('Tasks')
        ->assertPathIs('/tasks')
        ->wait(1)
        ->assertSee('Persistence probe');

    // A full page load restores the same toast from sessionStorage.
    $page->refresh()
        ->wait(1)
        ->assertSee('Persistence probe');

    // A short-lived toast expires on its own while the live one stays…
    $page->script("window.dispatchEvent(new CustomEvent('toast', { detail: { title: 'Ephemeral probe', duration: 3000 } }))");
    $page->wait(1)
        ->assertSee('Ephemeral probe');
    $page->wait(3)
        ->assertDontSee('Ephemeral probe')
        ->assertSee('Persistence probe');

    // …and navigating on must not resurrect the expired toast.
    $page->click('Plans')
        ->assertPathIs('/plans')
        ->wait(1)
        ->assertSee('Persistence probe')
        ->assertDontSee('Ephemeral probe');
});

it('holds the deadline while a toast is hovered and resumes it on leave', function () {
    User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);

    $page = visit('/login');

    $page->fill('email', 'amir@example.com')
        ->fill('password', 'password')
        ->click('Sign in')
        ->assertPathIs('/dashboard')
        ->wait(1);

    $page->script("window.dispatchEvent(new CustomEvent('toast', { detail: { title: 'Paused probe', duration: 3000 } }))");
    $page->wait(1)
        ->assertSee('Paused probe');

    // Hover pauses the countdown: real time passes the toast's deadline while
    // the pointer rests on it (toasts live in per-position containers, so find
    // the probe by its title rather than by DOM order).
    $page->script("const toast = [...document.querySelectorAll('.mine-toast-enter')].find((el) => el.querySelector('h3')?.textContent === 'Paused probe'); if (! toast) throw new Error('Paused probe toast not found'); toast.dispatchEvent(new MouseEvent('mouseenter'))");
    $page->wait(4)
        ->assertSee('Paused probe');

    // Leaving re-arms the deadline from the frozen remainder, so it expires.
    $page->script("const toast = [...document.querySelectorAll('.mine-toast-enter')].find((el) => el.querySelector('h3')?.textContent === 'Paused probe'); if (! toast) throw new Error('Paused probe toast not found'); toast.dispatchEvent(new MouseEvent('mouseleave'))");
    $page->wait(3)
        ->assertDontSee('Paused probe');
});
