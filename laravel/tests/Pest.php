<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(LazilyRefreshDatabase::class)
    ->in('Feature', 'Unit', 'Browser', '../resources/views');

/*
|--------------------------------------------------------------------------
| Browser
|--------------------------------------------------------------------------
|
| The browser tier is opt-in (never part of `php artisan test`): run it with
| `composer test:browser`. Every failed assertion saves a PNG to
| `tests/Browser/Screenshots/` (on the bind mount, so open it straight from
| the host). Use `$page->screenshot()` for on-demand captures, or run with
| `--debug` to pause on the first failure. The container is headless, so
| `--headed` is not available here.
|
*/

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/
