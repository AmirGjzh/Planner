<?php

use App\Enums\UserGender;
use App\Models\User;
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
        ->assertSee('My Profile')
        ->assertSee('amir_user')
        ->assertSee('amir@example.com')
        ->assertSee('Amir')
        ->assertSee('Planner')
        ->assertSee('Male')
        ->assertSee('Iran');
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
        ]);
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
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->username)->toBe('new_user')
        ->and($user->firstname)->toBe('New')
        ->and($user->lastname)->toBe('Name')
        ->and($user->gender)->toBe(UserGender::Female)
        ->and($user->country)->toBe('DE')
        ->and($user->birthday->format('Y-m-d'))->toBe('1998-05-20');
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
        ->assertRedirect(route('login'));

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
        ->assertRedirect(route('login'));
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
