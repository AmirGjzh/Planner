<?php

namespace App\Support;

final class PersianNumber
{
    /**
     * @var array<string, string>
     */
    private const MAP = [
        '0' => '۰',
        '1' => '۱',
        '2' => '۲',
        '3' => '۳',
        '4' => '۴',
        '5' => '۵',
        '6' => '۶',
        '7' => '۷',
        '8' => '۸',
        '9' => '۹',
    ];

    /**
     * Render a value with locale-appropriate digits: identity in en, Persian
     * digits in fa. The single call site idiom for every rendered count,
     * percentage, and :count replacement (see .ai/rules/lang.md).
     */
    public static function localize(int|float|string|null $value): string
    {
        return app()->isLocale('en') ? (string) $value : self::convert($value);
    }

    public static function convert(int|string|float|null $value): string
    {
        return strtr((string) $value, self::MAP);
    }
}
