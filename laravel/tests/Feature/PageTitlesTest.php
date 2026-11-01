<?php

use App\Models\User;

it('gives every app page a document title', function () {
    $user = User::factory()->create();

    $titles = [
        '/dashboard' => 'Dashboard',
        '/profile' => 'Profile',
        '/categories' => 'Categories',
        '/plans' => 'Plans',
        '/tasks' => 'Tasks',
        '/reports' => 'Reports',
    ];

    foreach ($titles as $route => $title) {
        $this->actingAs($user)
            ->get($route)
            ->assertSuccessful()
            ->assertSee("<title>{$title} — ".config('app.name').'</title>', false);
    }
});
