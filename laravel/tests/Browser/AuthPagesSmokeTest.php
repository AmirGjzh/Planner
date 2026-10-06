<?php

use App\Models\User;
use Illuminate\Support\Facades\Password;

it('has no smoke or accessibility issues on the auth pages', function () {
    $user = User::factory()->create(['email' => 'amir@example.com']);
    $token = Password::createToken($user);

    $pages = visit([
        '/login',
        '/register',
        '/forgot-password',
        "/reset-password?token={$token}&email={$user->email}",
    ]);

    // Let the card entrance animation settle so axe reads the real colors.
    $pages->wait(1.5);

    $pages->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    [$login, $register, $forgot, $reset] = $pages;

    $login->assertSee('Welcome back');
    $register->assertSee('Welcome to Planner');
    $forgot->assertSee('Reset your password');
    $reset->assertSee('Choose a new password');

    // The canvas carries the page color, so the overscroll gap is never white.
    $login->assertScript(
        'getComputedStyle(document.documentElement).backgroundColor !== "rgba(0, 0, 0, 0)"',
        true,
    );
});
