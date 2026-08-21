<?php

use App\Models\User;

it('builds initials from firstname and lastname', function () {
    $user = User::factory()->make([
        'firstname' => 'Amir',
        'lastname' => 'Planner',
        'username' => 'amir_user',
    ]);

    expect($user->initials())->toBe("A\u{200C}P");
});

it('falls back to the first two username characters', function () {
    $user = User::factory()->make([
        'firstname' => null,
        'lastname' => null,
        'username' => 'amir',
    ]);

    expect($user->initials())->toBe("A\u{200C}M");
});

it('handles a single-character username', function () {
    $user = User::factory()->make([
        'firstname' => null,
        'lastname' => null,
        'username' => 'a',
    ]);

    expect($user->initials())->toBe('A');
});

it('supports multibyte names', function () {
    $user = User::factory()->make([
        'firstname' => 'امیر',
        'lastname' => 'علی',
    ]);

    expect($user->initials())->toBe("ا\u{200C}ع");
});
