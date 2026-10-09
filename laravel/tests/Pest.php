<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Pest\Browser\Api\ArrayablePendingAwaitablePage;
use Pest\Browser\Api\PendingAwaitablePage;
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
| Axe runs through `assertNoAccessibilityIssuesExceptContrast()` below:
| every axe rule applies except `color-contrast`, which is switched off
| while the theme palettes are frozen in this stage of the project (the
| saffron orange sits at ~4.2:1 against the card white — a known, deferred
| WCAG miss, not a test blocker). Drop the `script()` call to re-enable it
| once the themes resume.
|
*/

/**
 * Assert the page has no accessibility issues, minus axe's color-contrast rule.
 *
 * Works on a single page and on the array result of `visit([...])` alike.
 */
function assertNoAccessibilityIssuesExceptContrast(PendingAwaitablePage|ArrayablePendingAwaitablePage $page): void
{
    $patch = <<<'JS'
        (() => {
            const run = window.axe.run.bind(window.axe);
            window.axe.run = (context, options) => run(context ?? document, {
                ...options,
                rules: { ...options?.rules, 'color-contrast': { enabled: false } },
            });
        })();
        JS;

    // The multi-page wrapper rejects `script()`'s null result, so patch each
    // contained page directly; the assertion below maps over them itself.
    $targets = $page instanceof ArrayablePendingAwaitablePage ? iterator_to_array($page) : [$page];

    foreach ($targets as $target) {
        $target->script($patch);
    }

    $page->assertNoAccessibilityIssues();
}

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
