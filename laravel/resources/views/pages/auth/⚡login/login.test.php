<?php

use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

beforeEach(function () {
    RateLimiter::clear('login:127.0.0.1');
});

it('renders successfully', function () {
    Livewire::test('pages::auth.login')
        ->assertStatus(200)
        ->assertSee('Welcome back');
});

it('links to the forgot-password page', function () {
    Livewire::test('pages::auth.login')
        ->assertSee(route('password.request'));
});

it('re-dispatches a flashed session toast on mount', function () {
    session()->flash('toast', [
        'title' => 'You\'re signed in',
        'variant' => 'success',
        'duration' => 6000,
        'position' => 'bottom-center',
    ]);

    Livewire::test('pages::auth.login')
        ->assertDispatched('toast',
            title: 'You\'re signed in',
            variant: 'success',
        );
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
        ->assertHasErrors(['email' => ['email']])
        ->assertSee('Enter a valid email.');
    $this->assertGuest();
});

it('dispatches focus-first-error when validation fails', function () {
    Livewire::test('pages::auth.login')
        ->call('login')
        ->assertHasErrors(['email' => ['required']])
        ->assertDispatched('focus-first-error');
});

it('wires invalid fields to their error messages', function () {
    Livewire::test('pages::auth.login')
        ->set('email', 'bad-email')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email' => ['email']])
        ->assertSee('aria-invalid="true"', false)
        ->assertSee('aria-describedby="email-error"', false)
        ->assertSee('id="email-error"', false);
});

it('renders the email field with semantic input attributes', function () {
    Livewire::test('pages::auth.login')
        ->assertSee('type="email"', false)
        ->assertSee('spellcheck="false"', false)
        ->assertSee('novalidate', false);
});

it('labels the password visibility toggle', function () {
    Livewire::test('pages::auth.login')
        ->assertSee('aria-label="Show password"', false)
        ->assertSee(':aria-pressed="show"', false);
});

it('shows the email field placeholder label', function () {
    Livewire::test('pages::auth.login')
        ->assertSee('placeholder="'.__('Your email').'"', false);
});

it('animates only explicit CSS properties', function () {
    Livewire::test('pages::auth.login')
        ->assertDontSee('transition-all', false);
});

it('dims the submit button while it is busy', function () {
    Livewire::test('pages::auth.login')
        ->assertSee('disabled:opacity-60', false)
        ->assertSee('disabled:cursor-wait', false);
});

it('trims the email before validating', function () {
    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);

    Livewire::test('pages::auth.login')
        ->set('email', '  amir@example.com  ')
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertNotDispatched('focus-first-error')
        ->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

it('rejects an oversized password', function () {
    Livewire::test('pages::auth.login')
        ->set('email', 'amir@example.com')
        ->set('password', str_repeat('a', 257))
        ->call('login')
        ->assertHasErrors(['password' => ['max']]);
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
        ->assertSet('password', '')
        ->assertSee('role="alert"', false)
        ->assertSee('Check your email and password, then try again.');
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

it('flashes a success toast after signing in', function () {
    User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);

    Livewire::test('pages::auth.login')
        ->set('email', 'amir@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    $toast = session('toast');

    expect($toast)->toBeArray()
        ->and($toast['title'])->toBe(__('You\'re signed in'))
        ->and($toast['variant'])->toBe('success');
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
        ->assertSet('login_error', 'rate_limited')
        ->assertSee('Too many sign-in attempts!');
    $this->assertGuest();
});

it('flashes the sign-in toast in the user locale', function () {
    User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
        'locale' => 'fa',
    ]);

    Livewire::test('pages::auth.login')
        ->set('email', 'amir@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    $toast = session('toast');

    expect($toast)->toBeArray()
        ->and($toast['title'])->toBe('وارد شدی')
        ->and($toast['variant'])->toBe('success');
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

it('redirects to the originally requested page after signing in', function () {
    User::factory()->create([
        'email' => 'amir@example.com',
        'password' => 'password',
    ]);
    session()->put('url.intended', url('/tasks'));

    Livewire::test('pages::auth.login')
        ->set('email', 'amir@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(url('/tasks'));
});

it('consumes the session hint cookie and shows an expired-session toast', function () {
    Livewire::withCookies(['session_hint' => '1'])
        ->test('pages::auth.login')
        ->assertDispatched('toast',
            title: 'Your session has expired, sign in again',
            variant: 'info',
        );

    $queued = collect(Cookie::getQueuedCookies())
        ->first(fn ($cookie) => $cookie->getName() === 'session_hint');

    expect($queued)->not->toBeNull();
});

it('shows no expired-session toast without the hint cookie', function () {
    Livewire::test('pages::auth.login')
        ->assertNotDispatched('toast');
});
