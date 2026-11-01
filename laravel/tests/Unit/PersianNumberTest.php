<?php

use App\Support\PersianNumber;

it('renders western digits in en', function () {
    app()->setLocale('en');

    expect(PersianNumber::localize(60))->toBe('60')
        ->and(PersianNumber::localize('۶۰'))->toBe('۶۰')
        ->and(PersianNumber::localize(null))->toBe('');
});

it('renders persian digits in fa', function () {
    app()->setLocale('fa');

    expect(PersianNumber::localize(60))->toBe('۶۰')
        ->and(PersianNumber::localize(2))->toBe('۲')
        ->and(PersianNumber::localize(null))->toBe('');
});
