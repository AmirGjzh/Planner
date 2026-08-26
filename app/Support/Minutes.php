<?php

namespace App\Support;

final class Minutes
{
    public static function format(int $minutes): string
    {
        if (app()->isLocale('fa')) {
            return self::formatFa($minutes);
        }

        if ($minutes === 0) {
            return '0m';
        }

        $hours = intdiv($minutes, 60);
        $remaining = $minutes % 60;

        if ($hours === 0) {
            return $remaining.'m';
        }

        if ($remaining === 0) {
            return $hours.'h';
        }

        return $hours.'h '.$remaining.'m';
    }

    private static function formatFa(int $minutes): string
    {
        $minuteWord = __('min');

        if ($minutes === 0) {
            return PersianNumber::convert(0).' '.$minuteWord;
        }

        $hours = intdiv($minutes, 60);
        $remaining = $minutes % 60;

        if ($hours === 0) {
            return PersianNumber::convert($remaining).' '.$minuteWord;
        }

        $hourPart = PersianNumber::convert($hours).' '.__('hour');

        if ($remaining === 0) {
            return $hourPart;
        }

        return $hourPart.' '.__('and').' '.PersianNumber::convert($remaining).' '.$minuteWord;
    }
}
