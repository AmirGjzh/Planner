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

it('links to the sign-in page', function () {
    Livewire::test('pages::auth.register')
        ->assertSee(route('login'));
});

it('renders the footer prompt without the OR separator', function () {
    Livewire::test('pages::auth.register')
        ->assertSee('Already have an account?')
        ->assertDontSee('role="separator"', false);
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
        ->assertHasErrors(['username'])
        ->assertSee('Start with a letter, use 3 to 30 characters.');
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
        ->assertHasErrors(['email' => ['email']])
        ->assertSee('Enter a valid email.');
});

it('rejects an oversized email', function () {
    Livewire::test('pages::auth.register')
        ->set('username', 'amir_user')
        ->set('email', str_repeat('a', 300).'@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasErrors(['email' => ['max']]);
});

it('dispatches focus-first-error when validation fails', function () {
    Livewire::test('pages::auth.register')
        ->call('register')
        ->assertHasErrors(['username' => ['required']])
        ->assertDispatched('focus-first-error');
});

it('wires invalid fields to their error messages', function () {
    Livewire::test('pages::auth.register')
        ->set('username', 'amir_user')
        ->set('email', 'bad-email')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasErrors(['email' => ['email']])
        ->assertSee('aria-invalid="true"', false)
        ->assertSee('aria-describedby="email-error"', false)
        ->assertSee('id="email-error"', false);
});

it('renders the email field with semantic input attributes', function () {
    Livewire::test('pages::auth.register')
        ->assertSee('type="email"', false)
        ->assertSee('spellcheck="false"', false)
        ->assertSee('novalidate', false);
});

it('labels the password visibility toggles', function () {
    $html = Livewire::test('pages::auth.register')->html();

    expect(substr_count($html, 'aria-label="Show password"'))->toBe(2)
        ->and(substr_count($html, ':aria-pressed="show"'))->toBe(2);
});

it('shows the username field placeholder label', function () {
    Livewire::test('pages::auth.register')
        ->assertSee('placeholder="'.__('Your username').'"', false);
});

it('animates only explicit CSS properties', function () {
    Livewire::test('pages::auth.register')
        ->assertDontSee('transition-all', false);
});

it('dims the submit button while it is busy', function () {
    Livewire::test('pages::auth.register')
        ->assertSee('disabled:opacity-60', false)
        ->assertSee('disabled:cursor-wait', false);
});

it('renders the fields with autofill autocomplete tokens', function () {
    $html = Livewire::test('pages::auth.register')->html();

    expect(substr_count($html, 'autocomplete="email"'))->toBe(1)
        ->and(substr_count($html, 'autocomplete="new-password"'))->toBe(2);
});

it('trims the email before validating', function () {
    Livewire::test('pages::auth.register')
        ->set('username', 'amir_user')
        ->set('email', '  amir@example.com  ')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));

    expect(User::firstWhere('email', 'amir@example.com'))->not->toBeNull();
});

it('trims the username before validating', function () {
    Livewire::test('pages::auth.register')
        ->set('username', '  amir_user  ')
        ->set('email', 'amir@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));

    expect(User::firstWhere('username', 'amir_user'))->not->toBeNull();
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

it('rejects an oversized password', function () {
    Livewire::test('pages::auth.register')
        ->set('username', 'amir_user')
        ->set('email', 'amir@example.com')
        ->set('password', str_repeat('a', 257))
        ->set('password_confirmation', str_repeat('a', 257))
        ->call('register')
        ->assertHasErrors(['password' => ['max']]);
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
