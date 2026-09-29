<?php

use App\Actions\Auth\ResetPasswordAction;
use App\Enums\ResetPasswordResult;
use App\Models\User;
use Illuminate\Support\Facades\Password;

it('stamps the session auth time on the first authenticated request', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/dashboard')->assertSuccessful();

    expect(session()->get('auth_time'))->not->toBeNull();
});

it('keeps a session when the password is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/dashboard')->assertSuccessful();

    $this->travel(1)->seconds();

    $this->get('/dashboard')->assertSuccessful();
});

it('signs out a session whose password changed after its auth time', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/dashboard')->assertSuccessful();

    $this->travel(1)->seconds();
    $user->forceFill(['password_changed_at' => now()])->save();

    $this->get('/dashboard')
        ->assertRedirect('/login')
        ->assertSessionHas('toast');
    $this->assertGuest();
});

it('invalidates active sessions when the recovery flow resets the password', function () {
    $user = User::factory()->create(['password' => 'old-password']);

    $this->actingAs($user)->get('/dashboard')->assertSuccessful();

    $this->travel(1)->seconds();
    $token = Password::broker()->createToken($user);
    $result = app(ResetPasswordAction::class)
        ->execute($user->email, $token, 'new-password-123', request());

    $user->refresh();

    expect($result)->toBe(ResetPasswordResult::Reset)
        ->and($user->password_changed_at)->not->toBeNull();

    $this->get('/dashboard')->assertRedirect('/login');
    $this->assertGuest();
});
