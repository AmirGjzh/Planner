<?php

it('toggles the password field between hidden and shown', function () {
    visit('/login')
        ->assertAttribute('#password', 'type', 'password')
        ->click('[aria-label="Show password"]')
        ->assertAttribute('#password', 'type', 'text')
        ->assertAttribute('[aria-label="Show password"]', 'aria-pressed', 'true')
        ->click('[aria-label="Show password"]')
        ->assertAttribute('#password', 'type', 'password')
        ->assertAttribute('[aria-label="Show password"]', 'aria-pressed', 'false');
});
