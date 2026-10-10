<?php

use App\Enums\UserGender;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

function profileUser(array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'username' => 'profile_user_'.fake()->unique()->numberBetween(1000, 999999),
        'country' => 'IR',
        'birthday' => '2000-01-15',
        'gender' => UserGender::Male,
    ], $attributes));
}

it('renders the authenticated user profile', function () {
    $user = profileUser([
        'username' => 'amir_user',
        'email' => 'amir@example.com',
        'firstname' => 'Amir',
        'lastname' => 'Planner',
    ]);

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->assertStatus(200)
        ->assertSee('My profile')
        ->assertSee('amir_user')
        ->assertSee('amir@example.com')
        ->assertSee('Amir')
        ->assertSee('Planner')
        ->assertSee('Male')
        ->assertSee('Iran');
});

it('renders without database queries', function () {
    $user = profileUser();

    $this->expectsDatabaseQueryCount(0);

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->assertStatus(200);
});

it('fills form properties from the authenticated user', function () {
    $user = profileUser([
        'username' => 'amir_user',
        'firstname' => 'Amir',
        'lastname' => 'Planner',
    ]);

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->assertSet('username', 'amir_user')
        ->assertSet('firstname', 'Amir')
        ->assertSet('lastname', 'Planner')
        ->assertSet('birthday', '2000-01-15')
        ->assertSet('gender', 'male')
        ->assertSet('country', 'IR');
});

it('ships the parity markup: focus wiring, autofill tokens, close labels, headings, and overflow guards', function () {
    $user = profileUser([
        'username' => 'amir_user',
        'email' => 'amir@example.com',
    ]);

    $html = Livewire::actingAs($user)
        ->test('pages::profile')
        ->html();

    expect(substr_count($html, 'novalidate'))->toBe(3)
        ->and(substr_count($html, 'focus-first-error.window'))->toBe(3)
        ->and(substr_count($html, 'autocomplete="current-password"'))->toBe(2)
        ->and(substr_count($html, 'autocomplete="new-password"'))->toBe(2)
        ->and(substr_count($html, 'aria-label="Close"'))->toBe(3)
        ->and(substr_count($html, '<h1'))->toBe(1)
        ->and(substr_count($html, 'wire:loading:disabled'))->toBe(3)
        ->and($html)->toContain('mine-text-primary mb-1 truncate text-base font-semibold">amir_user<')
        ->and($html)->toContain('mine-text-secondary truncate text-sm font-medium">amir@example.com<');
});

it('validates required username', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('username', '')
        ->call('editProfile')
        ->assertHasErrors(['username' => ['required']]);
});

it('validates username format', function (string $username) {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('username', $username)
        ->call('editProfile')
        ->assertHasErrors(['username']);
})->with([
    'starts with number' => '1amir',
    'too short' => 'am',
    'contains space' => 'amir user',
    'contains symbol' => 'amir@user',
]);

it('validates text field length', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('firstname', str_repeat('a', 51))
        ->set('lastname', str_repeat('b', 51))
        ->call('editProfile')
        ->assertHasErrors([
            'firstname' => ['max'],
            'lastname' => ['max'],
        ]);
});

it('validates gender, country, and birth date', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('gender', 'other')
        ->set('country', 'XX')
        ->set('birthday', now()->addDay()->format('Y-m-d'))
        ->call('editProfile')
        ->assertHasErrors([
            'gender',
            'country',
            'birthday' => ['before_or_equal'],
        ])
        ->assertSee('Birthdate must be today or earlier');
});

it('dispatches focus-first-error when the edit profile validation fails', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('username', '')
        ->call('editProfile')
        ->assertHasErrors(['username' => ['required']])
        ->assertDispatched('focus-first-error');
});

it('updates all editable profile fields', function () {
    $user = profileUser([
        'username' => 'old_user',
    ]);

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('username', 'new_user')
        ->set('firstname', 'New')
        ->set('lastname', 'Name')
        ->set('gender', 'female')
        ->set('country', 'DE')
        ->set('birthday', '1998-05-20')
        ->call('editProfile')
        ->assertHasNoErrors()
        ->assertDispatched('close-modal', id: 'edit-profile-form')
        ->assertDispatched('toast',
            title: __('Profile updated'),
            variant: 'info',
        )
        ->assertDispatched('profile-updated',
            username: 'new_user',
            email: $user->email,
            firstname: 'New',
            lastname: 'Name',
            initials: 'N'."\u{200C}".'N',
        );

    $user->refresh();

    expect($user->username)->toBe('new_user')
        ->and($user->firstname)->toBe('New')
        ->and($user->lastname)->toBe('Name')
        ->and($user->gender)->toBe(UserGender::Female)
        ->and($user->country)->toBe('DE')
        ->and($user->birthday->format('Y-m-d'))->toBe('1998-05-20');
});

it('edits the profile with exactly the update query', function () {
    $user = profileUser();

    $this->expectsDatabaseQueryCount(1);

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('firstname', 'Locked')
        ->call('editProfile')
        ->assertHasNoErrors();
});

it('stores blank optional fields as null', function () {
    $user = profileUser([
        'firstname' => 'Amir',
        'lastname' => 'Planner',
    ]);

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('firstname', '')
        ->set('lastname', '   ')
        ->set('gender')
        ->set('country')
        ->set('birthday')
        ->call('editProfile')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->firstname)->toBeNull()
        ->and($user->lastname)->toBeNull()
        ->and($user->gender)->toBeNull()
        ->and($user->country)->toBeNull()
        ->and($user->birthday)->toBeNull();
});

it('shows username taken error', function () {
    profileUser([
        'username' => 'taken_user',
    ]);

    $user = profileUser([
        'username' => 'amir_user',
    ]);

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('username', 'taken_user')
        ->call('editProfile')
        ->assertSet('edit_error', 'username_taken');

    expect($user->refresh()->username)->toBe('amir_user');
});

it('clears a previous profile edit error on a new submission', function () {
    profileUser([
        'username' => 'taken_user',
    ]);

    $user = profileUser([
        'username' => 'amir_user',
    ]);

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('username', 'taken_user')
        ->call('editProfile')
        ->assertSet('edit_error', 'username_taken')
        ->set('username', '')
        ->call('editProfile')
        ->assertSet('edit_error', null)
        ->assertHasErrors(['username' => ['required']]);

    expect($user->refresh()->username)->toBe('amir_user');
});

it('shows username taken error for mixed-case username', function () {
    profileUser([
        'username' => 'taken_user',
    ]);

    $user = profileUser([
        'username' => 'amir_user',
    ]);

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('username', 'Taken_User')
        ->call('editProfile')
        ->assertSet('edit_error', 'username_taken');

    expect($user->refresh()->username)->toBe('amir_user');
});

it('shows rate limit error after too many profile update attempts', function () {
    $user = profileUser(['username' => 'amir_user']);
    profileUser(['username' => 'taken_user']);

    foreach (range(1, 5) as $ignored) {
        Livewire::actingAs($user)
            ->test('pages::profile')
            ->set('username', 'taken_user')
            ->call('editProfile')
            ->assertSet('edit_error', 'username_taken');
    }

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('username', 'new_user')
        ->call('editProfile')
        ->assertSet('edit_error', 'rate_limited');
});

it('allows profile updates again after one minute', function () {
    $user = profileUser(['username' => 'amir_user']);
    profileUser(['username' => 'taken_user']);

    foreach (range(1, 5) as $ignored) {
        Livewire::actingAs($user)
            ->test('pages::profile')
            ->set('username', 'taken_user')
            ->call('editProfile');
    }

    // The failed updates left the shared guard instance dirty in this single-app test run;
    // reload the row the way each real request would.
    $user->refresh();

    $this->travel(61)->seconds();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('firstname', 'Allowed')
        ->call('editProfile')
        ->assertHasNoErrors();

    expect($user->refresh()->firstname)->toBe('Allowed');
});

it('cancels edits and restores form state', function () {
    $user = profileUser([
        'username' => 'amir_user',
        'firstname' => 'Amir',
    ]);

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('username', 'changed_user')
        ->set('firstname', 'Changed')
        ->call('cancelEdit')
        ->assertSet('username', 'amir_user')
        ->assertSet('firstname', 'Amir');
});

it('dispatches focus-first-error when the delete account validation fails', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->call('deleteAccount')
        ->assertHasErrors(['password' => ['required']])
        ->assertDispatched('focus-first-error');
});

it('caps the delete password at 256 characters', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('password', str_repeat('a', 257))
        ->call('deleteAccount')
        ->assertHasErrors(['password' => 'max']);
});

it('names every profile dialog for screen readers', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->assertSee('aria-labelledby="delete-account-title"', false)
        ->assertSee('id="delete-account-title"', false)
        ->assertSee('aria-labelledby="change-password-title"', false)
        ->assertSee('aria-labelledby="edit-profile-title"', false);
});

it('shows wrong password error', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('password', 'wrong_password')
        ->call('deleteAccount')
        ->assertSet('delete_error', 'wrong_password');
});

it('shows rate limit error after too many delete attempts', function () {
    $user = profileUser();

    foreach (range(1, 5) as $ignored) {
        Livewire::actingAs($user)
            ->test('pages::profile')
            ->set('password', 'wrong_password')
            ->call('deleteAccount')
            ->assertSet('delete_error', 'wrong_password');
    }

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('password', 'wrong_password')
        ->call('deleteAccount')
        ->assertSet('delete_error', 'rate_limited');
});

it('deletes the account with correct password and redirects', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('password', 'password')
        ->call('deleteAccount')
        ->assertRedirect(route('home'));

    $toast = session('toast');

    expect($toast)->toBeArray()
        ->and($toast['title'])->toBe(__('Account deleted'))
        ->and($toast['variant'])->toBe('success');

    $this->assertSoftDeleted($user);
});

it('allows deletion again after rate limit expires', function () {
    $user = profileUser();

    foreach (range(1, 5) as $ignored) {
        Livewire::actingAs($user)
            ->test('pages::profile')
            ->set('password', 'wrong_password')
            ->call('deleteAccount');
    }

    $this->travel(61)->seconds();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('password', 'password')
        ->call('deleteAccount')
        ->assertRedirect(route('home'));
});

it('clears a previous wrong-password error on a new delete attempt', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('password', 'wrong_password')
        ->call('deleteAccount')
        ->assertSet('delete_error', 'wrong_password')
        ->set('password', '')
        ->call('deleteAccount')
        ->assertHasErrors(['password' => 'required'])
        ->assertSet('delete_error', null);
});

it('cancels delete and resets form state', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('password', 'somepassword')
        ->call('cancelDelete')
        ->assertSet('password', '')
        ->assertSet('delete_error', null);
});

it('renders the change password trigger and modal fields', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->assertSee('Change password')
        ->assertSee('Current password')
        ->assertSee('New password')
        ->assertSee('Confirm new password');
});

it('validates the change password fields', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->call('changePassword')
        ->assertHasErrors([
            'current_password' => ['required'],
            'new_password' => ['required'],
            'new_password_confirmation' => ['required'],
        ]);

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('current_password', 'password')
        ->set('new_password', 'short')
        ->set('new_password_confirmation', 'different')
        ->call('changePassword')
        ->assertHasErrors(['new_password' => ['confirmed', 'min']]);
});

it('caps the current password at 256 characters', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('current_password', str_repeat('a', 257))
        ->call('changePassword')
        ->assertHasErrors(['current_password' => 'max']);
});

it('dispatches focus-first-error when the change password validation fails', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->call('changePassword')
        ->assertDispatched('focus-first-error');
});

it('changes the password and confirms with a toast', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('current_password', 'password')
        ->set('new_password', 'brand-new-password')
        ->set('new_password_confirmation', 'brand-new-password')
        ->call('changePassword')
        ->assertHasNoErrors()
        ->assertSet('change_error', null)
        ->assertDispatched('close-modal', id: 'change-password-form')
        ->assertDispatched('toast',
            title: __('Password changed'),
            variant: 'success',
        )
        ->assertSet('current_password', '')
        ->assertSet('new_password', '')
        ->assertSet('new_password_confirmation', '');

    expect(Hash::check('brand-new-password', $user->fresh()->password))->toBeTrue();
});

it('deletes the account with exactly the three update queries', function () {
    $user = profileUser();

    $this->expectsDatabaseQueryCount(3);

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('password', 'password')
        ->call('deleteAccount')
        ->assertRedirect(route('home'));
});

it('changes the password with exactly the update query', function () {
    $user = profileUser([
        'password' => 'current-password',
    ]);

    $this->expectsDatabaseQueryCount(1);

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('current_password', 'current-password')
        ->set('new_password', 'brand-new-password')
        ->set('new_password_confirmation', 'brand-new-password')
        ->call('changePassword')
        ->assertHasNoErrors();
});

it('shows wrong current password error', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('current_password', 'wrong_password')
        ->set('new_password', 'brand-new-password')
        ->set('new_password_confirmation', 'brand-new-password')
        ->call('changePassword')
        ->assertSet('change_error', 'wrong_password')
        ->assertSee('Wrong password!');
});

it('shows the same-password alert when the new password equals the current one', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('current_password', 'password')
        ->set('new_password', 'password')
        ->set('new_password_confirmation', 'password')
        ->call('changePassword')
        ->assertSet('change_error', 'same_as_current')
        ->assertSee('New password is equal to current one.');
});

it('shows rate limit error after too many change password attempts', function () {
    $user = profileUser();

    foreach (range(1, 5) as $ignored) {
        Livewire::actingAs($user)
            ->test('pages::profile')
            ->set('current_password', 'wrong_password')
            ->set('new_password', 'brand-new-password')
            ->set('new_password_confirmation', 'brand-new-password')
            ->call('changePassword')
            ->assertSet('change_error', 'wrong_password');
    }

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('current_password', 'wrong_password')
        ->set('new_password', 'brand-new-password')
        ->set('new_password_confirmation', 'brand-new-password')
        ->call('changePassword')
        ->assertSet('change_error', 'rate_limited');
});

it('clears a previous change error on a new submission', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('current_password', 'wrong_password')
        ->set('new_password', 'brand-new-password')
        ->set('new_password_confirmation', 'brand-new-password')
        ->call('changePassword')
        ->assertSet('change_error', 'wrong_password')
        ->set('current_password', '')
        ->call('changePassword')
        ->assertHasErrors(['current_password' => 'required'])
        ->assertSet('change_error', null);
});

it('cancels change and resets form state', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('current_password', 'wrong_password')
        ->set('new_password', 'brand-new-password')
        ->set('new_password_confirmation', 'brand-new-password')
        ->call('changePassword')
        ->assertSet('change_error', 'wrong_password')
        ->call('cancelChange')
        ->assertSet('current_password', '')
        ->assertSet('new_password', '')
        ->assertSet('new_password_confirmation', '')
        ->assertSet('change_error', null);
});
