<?php

namespace App\Support;

use Carbon\CarbonInterface;
use IntlDateFormatter;
use RuntimeException;

final class Jalali
{
    public static function format(CarbonInterface $date, string $pattern): string
    {
        $formatter = IntlDateFormatter::create(
            'fa_IR@calendar=persian',
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            config('app.timezone'),
            IntlDateFormatter::TRADITIONAL,
            $pattern,
        );

        if ($formatter === null) {
            throw new RuntimeException('Unable to create the Persian (Jalali) date formatter.');
        }

        return (string) $formatter->format($date);
    }
}
