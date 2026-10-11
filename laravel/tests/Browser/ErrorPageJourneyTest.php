<?php

it('renders an accessible themed error page', function () {
    $page = visit('/this-page-does-not-exist');

    $page->wait(1)
        ->assertSee('Page not found');

    assertNoAccessibilityIssuesExceptContrast($page);
});
