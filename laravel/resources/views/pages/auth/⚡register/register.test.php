<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

beforeEach(function () {
    RateLimiter::clear('register:127.0.0.1');
});

it('renders successfully', function () {
    Livewire::test('pages::auth.register')
        ->assertStatus(200)
        ->assertSee('Welcome to Planner');
});

it('validates required fields', function () {
    Livewire::test('pages::auth.register')
        ->call('register')
        ->assertHasErrors([
            'username' => ['required'],
            'email' => ['required'],
            'password' => ['required'],
            'password_confirmation' => ['required'],
        ]);
});

it('validates username format', function (string $username) {
    Livewire::test('pages::auth.register')
        ->set('username', $username)
        ->set('email', 'amir@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasErrors(['username']);
})->with([
    'starts with number' => '1amir',
    'too short' => 'am',
    'contains space' => 'amir user',
    'contains symbol' => 'amir@user',
]);

it('validates email format', function () {
    Livewire::test('pages::auth.register')
        ->set('username', 'amir_user')
        ->set('email', 'bad-email')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasErrors(['email' => ['email']]);
});

it('validates password confirmation', function () {
    Livewire::test('pages::auth.register')
        ->set('username', 'amir_user')
        ->set('email', 'amir@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'different-password')
        ->call('register')
        ->assertHasErrors(['password' => ['confirmed']]);
});

it('validates password length', function () {
    Livewire::test('pages::auth.register')
        ->set('username', 'amir_user')
        ->set('email', 'amir@example.com')
        ->set('password', 'short')
        ->set('password_confirmation', 'short')
        ->call('register')
        ->assertHasErrors(['password' => ['min']]);
});

it('shows username taken error', function () {
    User::factory()->create([
        'username' => 'amir_user',
        'email' => 'old@example.com',
    ]);

    Livewire::test('pages::auth.register')
        ->set('username', 'amir_user')
        ->set('email', 'amir@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertSet('register_error', 'username_taken');
});

it('clears a previous register error on a new submission', function () {
    User::factory()->create([
        'username' => 'amir_user',
        'email' => 'old@example.com',
    ]);

    Livewire::test('pages::auth.register')
        ->set('username', 'amir_user')
        ->set('email', 'amir@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertSet('register_error', 'username_taken')
        ->set('username', '')
        ->set('email', '')
        ->set('password', '')
        ->set('password_confirmation', '')
        ->call('register')
        ->assertSet('register_error', null)
        ->assertHasErrors([
            'username' => ['required'],
            'email' => ['required'],
            'password' => ['required'],
            'password_confirmation' => ['required'],
        ]);
});

it('shows username taken error for mixed-case username', function () {
    User::factory()->create([
        'username' => 'amir_user',
        'email' => 'old@example.com',
    ]);

    Livewire::test('pages::auth.register')
        ->set('username', 'Amir_User')
        ->set('email', 'amir@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertSet('register_error', 'username_taken');
});

it('shows email taken error', function () {
    User::factory()->create([
        'username' => 'old_user',
        'email' => 'amir@example.com',
    ]);

    Livewire::test('pages::auth.register')
        ->set('username', 'amir_user')
        ->set('email', 'amir@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertSet('register_error', 'email_taken');
});

it('creates the user and redirects to login', function () {
    Livewire::test('pages::auth.register')
        ->set('username', 'amir_user')
        ->set('email', 'amir@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertRedirect(route('login'));

    $user = User::firstWhere('email', 'amir@example.com');

    expect($user)->not->toBeNull()
        ->and($user->username)->toBe('amir_user')
        ->and($user->password)->not->toBe('password123');
});

it('flashes a success toast after registering', function () {
    Livewire::test('pages::auth.register')
        ->set('username', 'amir_user')
        ->set('email', 'amir@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertRedirect(route('login'));

    $toast = session('toast');

    expect($toast)->toBeArray()
        ->and($toast['title'])->toBe(__('Your account is ready, sign in to continue'))
        ->and($toast['variant'])->toBe('success');
});

it('does not authenticate the user after registration', function () {
    Livewire::test('pages::auth.register')
        ->set('username', 'amir_user')
        ->set('email', 'amir@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

it('rate limits after too many failed register attempts', function () {
    User::factory()->create([
        'username' => 'taken_user',
        'email' => 'old@example.com',
    ]);

    foreach (range(1, 5) as $ignored) {
        Livewire::test('pages::auth.register')
            ->set('username', 'taken_user')
            ->set('email', 'amir@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register');
    }

    Livewire::test('pages::auth.register')
        ->set('username', 'amir_user')
        ->set('email', 'amir@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertSet('register_error', 'rate_limited');
});

it('allows registration again after one minute', function () {
    User::factory()->create([
        'username' => 'taken_user',
        'email' => 'old@example.com',
    ]);

    foreach (range(1, 5) as $ignored) {
        Livewire::test('pages::auth.register')
            ->set('username', 'taken_user')
            ->set('email', 'amir@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register');
    }

    $this->travel(61)->seconds();

    Livewire::test('pages::auth.register')
        ->set('username', 'amir_user')
        ->set('email', 'amir@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertRedirect(route('login'));

    expect(User::where('email', 'amir@example.com')->exists())->toBeTrue();
});
