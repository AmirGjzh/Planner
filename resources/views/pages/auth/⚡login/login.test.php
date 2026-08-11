<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

beforeEach(function () {
    RateLimiter::clear('login:amir@example.com|127.0.0.1');
});

it('renders successfully', function () {
    Livewire::test('pages::auth.login')
        ->assertStatus(200)
        ->assertSee('Welcome back');
});

it('validates required fields', function () {
    Livewire::test('pages::auth.login')
        ->call('login')
        ->assertHasErrors([
            'email' => ['required'],
            'password' => ['required'],
        ]);
    $this->assertGuest();
});

it('validates email format', function () {
    Livewire::test('pages::auth.login')
        ->set('email', 'bad-email')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email' => ['email']]);
    $this->assertGuest();
});

it('shows a login error for wrong credentials', function () {
    User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);
    Livewire::test('pages::auth.login')
        ->set('email', 'amir@example.com')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertSet('login_error', 'invalid')
        ->assertSet('password', '');
    $this->assertGuest();
});

it('authenticates and redirects with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);

    Livewire::test('pages::auth.login')
        ->set('email', 'amir@example.com')
        ->set('password', 'password')
        ->set('remember', true)
        ->call('login')
        ->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

it('rate limits after too many failed attempts', function () {
    User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);
    foreach (range(1, 5) as $ignored) {
        Livewire::test('pages::auth.login')
            ->set('email', 'amir@example.com')
            ->set('password', 'wrong-password')
            ->call('login');
    }
    Livewire::test('pages::auth.login')
        ->set('email', 'amir@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertSet('login_error', 'rate_limited');
    $this->assertGuest();
});

it('allows login again after one minute', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);
    foreach (range(1, 5) as $ignored) {
        Livewire::test('pages::auth.login')
            ->set('email', 'amir@example.com')
            ->set('password', 'wrong-password')
            ->call('login');
    }
    $this->travel(61)->seconds();
    Livewire::test('pages::auth.login')
        ->set('email', 'amir@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

it('clears a previous login error on a new submission', function () {
    User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);
    Livewire::test('pages::auth.login')
        ->set('email', 'amir@example.com')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertSet('login_error', 'invalid')
        ->set('email', '')
        ->set('password', '')
        ->call('login')
        ->assertSet('login_error', null)
        ->assertHasErrors([
            'email' => ['required'],
            'password' => ['required'],
        ]);
    $this->assertGuest();
});

it('blocks login for a soft-deleted account', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);
    $user->delete();

    Livewire::test('pages::auth.login')
        ->set('email', 'amir@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertSet('login_error', 'invalid');
    $this->assertGuest();
});
