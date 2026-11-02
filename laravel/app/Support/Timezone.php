<?php

namespace App\Support;

final class Timezone
{
    /**
     * The project default for every locale except Persian.
     */
    public const string DEFAULT = 'UTC';

    /**
     * Persian users plan their day in Tehran time; the app's date boundaries,
     * week start and Jalali rendering follow the signed-in language.
     */
    public static function forLocale(string $locale): string
    {
        return $locale === 'fa' ? 'Asia/Tehran' : self::DEFAULT;
    }

    public static function apply(string $locale): void
    {
        $timezone = self::forLocale($locale);

        config(['app.timezone' => $timezone]);
        date_default_timezone_set($timezone);
    }
}
