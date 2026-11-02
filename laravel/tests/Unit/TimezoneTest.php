<?php

it('applies Tehran time for the Persian locale', function () {
    app()->setLocale('fa');

    expect(config('app.timezone'))->toBe('Asia/Tehran')
        ->and(date_default_timezone_get())->toBe('Asia/Tehran')
        ->and(now()->getTimezone()->getName())->toBe('Asia/Tehran');
});

it('keeps UTC for every other locale', function () {
    app()->setLocale('fa');
    app()->setLocale('en');

    expect(config('app.timezone'))->toBe('UTC')
        ->and(date_default_timezone_get())->toBe('UTC')
        ->and(now()->getTimezone()->getName())->toBe('UTC');
});
