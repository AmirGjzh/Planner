<?php

it('redirects a guest from the dashboard to the sign-in page', function () {
    visit('/dashboard')
        ->assertPathIs('/login')
        ->assertSee('Welcome back');
});
