<?php

namespace App\Support;

final class PersianNumber
{
    private const PERSIAN_DIGITS = [
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

    public static function show(int|string $number): string
    {
        if ($number === '') {
            return '';
        }
        $result = '';
        foreach (mb_str_split((string) $number) as $digit) {
            $result .= self::PERSIAN_DIGITS[$digit] ?? $digit;
        }

        return $result;
    }
}
