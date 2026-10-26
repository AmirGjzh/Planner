<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use IntlCalendar;
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

    /**
     * @return array{start: string, end: string}
     */
    public static function weekBounds(CarbonInterface $date): array
    {
        $calendar = self::calendar();
        $calendar->setTime($date->getTimestampMs());
        $calendar->set(IntlCalendar::FIELD_DAY_OF_WEEK, $calendar->getFirstDayOfWeek());

        $start = self::calendarDate($calendar);

        $calendar->add(IntlCalendar::FIELD_DAY_OF_WEEK, 6);
        $end = self::calendarDate($calendar);

        return ['start' => $start, 'end' => $end];
    }

    /**
     * @return array{start: string, end: string}
     */
    public static function monthBounds(CarbonInterface $date): array
    {
        $calendar = self::calendar();
        $calendar->setTime($date->getTimestampMs());
        $calendar->set(IntlCalendar::FIELD_DAY_OF_MONTH, 1);
        $start = self::calendarDate($calendar);

        $calendar->add(IntlCalendar::FIELD_MONTH, 1);
        $calendar->add(IntlCalendar::FIELD_DATE, -1);
        $end = self::calendarDate($calendar);

        return ['start' => $start, 'end' => $end];
    }

    private static function calendar(): IntlCalendar
    {
        $calendar = IntlCalendar::createInstance(config('app.timezone'), 'fa_IR@calendar=persian');

        if ($calendar === null) {
            throw new RuntimeException('Unable to create the Persian (Jalali) calendar.');
        }

        return $calendar;
    }

    private static function calendarDate(IntlCalendar $calendar): string
    {
        return Carbon::createFromTimestamp(
            (int) ($calendar->getTime() / 1000),
            config('app.timezone'),
        )->startOfDay()->toDateString();
    }
}
