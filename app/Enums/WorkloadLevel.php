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

    public function label(): string
    {
        return match ($this) {
            self::None => 'No tasks',
            self::Light => 'Light',
            self::Moderate => 'Moderate',
            self::Heavy => 'Heavy',
            self::VeryHeavy => 'Very Heavy',
        };
    }

    public function rangeLabel(): string
    {
        return match ($this) {
            self::None => 'No tasks',
            self::Light => 'Light (1–120 min)',
            self::Moderate => 'Moderate (121–240 min)',
            self::Heavy => 'Heavy (241–360 min)',
            self::VeryHeavy => 'Very Heavy (361+ min)',
        };
    }

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
