<?php

namespace App\Enums;

enum WorkloadLevel: string
{
    case None = 'none';
    case Light = 'light';
    case Moderate = 'moderate';
    case Heavy = 'heavy';
    case VeryHeavy = 'very_heavy';

    public const LIGHT_MAX_MINUTES = 120;

    public const MODERATE_MAX_MINUTES = 240;

    public const HEAVY_MAX_MINUTES = 360;

    public static function forMinutes(int $minutes): self
    {
        return match (true) {
            $minutes <= 0 => self::None,
            $minutes <= self::LIGHT_MAX_MINUTES => self::Light,
            $minutes <= self::MODERATE_MAX_MINUTES => self::Moderate,
            $minutes <= self::HEAVY_MAX_MINUTES => self::Heavy,
            default => self::VeryHeavy,
        };
    }
}
