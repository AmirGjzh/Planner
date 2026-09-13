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

    public static function convert(int|string|float|null $value): string
    {
        return strtr((string) $value, self::MAP);
    }
}
