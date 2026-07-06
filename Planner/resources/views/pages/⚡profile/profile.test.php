<?php

use App\Enums\UserGender;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

beforeEach(function () {
    RateLimiter::clear('profile-update:1|127.0.0.1');
});

function profileUser(array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'user_name' => 'profile_user_'.fake()->unique()->numberBetween(1000, 999999),
        'country' => 'IR',
        'birth_date' => '2000-01-15',
        'gender' => UserGender::Male,
    ], $attributes));
}

it('renders the authenticated user profile', function () {
    $user = profileUser([
        'user_name' => 'amir_user',
        'email' => 'amir@example.com',
        'first_name' => 'Amir',
        'last_name' => 'Planner',
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
        'user_name' => 'amir_user',
        'first_name' => 'Amir',
        'last_name' => 'Planner',
    ]);

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->assertSet('user_name', 'amir_user')
        ->assertSet('first_name', 'Amir')
        ->assertSet('last_name', 'Planner')
        ->assertSet('birth_date', '2000-01-15')
        ->assertSet('gender', 'male')
        ->assertSet('country', 'IR');
});

it('validates required username', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('user_name', '')
        ->call('editProfile')
        ->assertHasErrors(['user_name' => ['required']]);
});

it('validates username format', function (string $username) {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('user_name', $username)
        ->call('editProfile')
        ->assertHasErrors(['user_name']);
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
        ->set('first_name', str_repeat('a', 51))
        ->set('last_name', str_repeat('b', 51))
        ->call('editProfile')
        ->assertHasErrors([
            'first_name' => ['max'],
            'last_name' => ['max'],
        ]);
});

it('validates gender, country, and birth date', function () {
    $user = profileUser();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('gender', 'other')
        ->set('country', 'XX')
        ->set('birth_date', now()->addDay()->format('Y-m-d'))
        ->call('editProfile')
        ->assertHasErrors([
            'gender',
            'country',
            'birth_date' => ['before_or_equal'],
        ]);
});

it('updates all editable profile fields', function () {
    $user = profileUser([
        'user_name' => 'old_user',
    ]);

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('user_name', 'new_user')
        ->set('first_name', 'New')
        ->set('last_name', 'Name')
        ->set('gender', 'female')
        ->set('country', 'DE')
        ->set('birth_date', '1998-05-20')
        ->call('editProfile')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->user_name)->toBe('new_user');
    expect($user->first_name)->toBe('New');
    expect($user->last_name)->toBe('Name');
    expect($user->gender)->toBe(UserGender::Female);
    expect($user->country)->toBe('DE');
    expect($user->birth_date->format('Y-m-d'))->toBe('1998-05-20');
});

it('stores blank optional fields as null', function () {
    $user = profileUser([
        'first_name' => 'Amir',
        'last_name' => 'Planner',
    ]);

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('first_name', '')
        ->set('last_name', '   ')
        ->set('gender', null)
        ->set('country', null)
        ->set('birth_date', null)
        ->call('editProfile')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->first_name)->toBeNull();
    expect($user->last_name)->toBeNull();
    expect($user->gender)->toBeNull();
    expect($user->country)->toBeNull();
    expect($user->birth_date)->toBeNull();
});

it('shows username taken error', function () {
    profileUser([
        'user_name' => 'taken_user',
    ]);

    $user = profileUser([
        'user_name' => 'amir_user',
    ]);

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('user_name', 'taken_user')
        ->call('editProfile')
        ->assertHasErrors('user_name');

    expect($user->refresh()->user_name)->toBe('amir_user');
});

it('shows rate limit error after too many profile updates', function () {
    $user = profileUser([
        'user_name' => 'amir_user',
    ]);

    foreach (range(1, 5) as $attempt) {
        Livewire::actingAs($user)
            ->test('pages::profile')
            ->set('first_name', 'Name '.$attempt)
            ->call('editProfile');
    }

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('first_name', 'Blocked')
        ->call('editProfile')
        ->assertHasErrors('profile');
});

it('allows profile updates again after one minute', function () {
    $user = profileUser([
        'user_name' => 'amir_user',
    ]);

    foreach (range(1, 5) as $attempt) {
        Livewire::actingAs($user)
            ->test('pages::profile')
            ->set('first_name', 'Name '.$attempt)
            ->call('editProfile');
    }

    $this->travel(61)->seconds();

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('first_name', 'Allowed')
        ->call('editProfile')
        ->assertHasNoErrors();

    expect($user->refresh()->first_name)->toBe('Allowed');
});

it('cancels edits and restores form state', function () {
    $user = profileUser([
        'user_name' => 'amir_user',
        'first_name' => 'Amir',
    ]);

    Livewire::actingAs($user)
        ->test('pages::profile')
        ->set('user_name', 'changed_user')
        ->set('first_name', 'Changed')
        ->call('cancelEdit')
        ->assertSet('user_name', 'amir_user')
        ->assertSet('first_name', 'Amir');
});
