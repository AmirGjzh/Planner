<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class Reicon
{
    private static ?array $icons = null;

    /**
     * @return array<string, array{O?: string, F?: string}>
     */
    private static function icons(): array
    {
        return self::$icons ??= json_decode(
            File::get(app_path('Support/reicon-icons.json')),
            true
        );
    }

    /**
     * Render a reicon SVG icon server-side.
     *
     * @param  array<string, string>  $attrs
     */
    public static function svg(
        ?string $name,
        string $weight = 'outline',
        int|string $size = 24,
        string $class = '',
        array $attrs = [],
    ): ?string {
        if ($name === null) {
            return null;
        }

        $data = self::icons()[$name] ?? null;

        if ($data === null) {
            return null;
        }

        $key = $weight === 'filled' ? 'F' : 'O';
        $inner = $data[$key]
            ?? $data['O']
            ?? null;

        if ($inner === null) {
            return null;
        }

        $size = (int) $size;
        $classAttr = $class !== '' ? ' class="reicon '.e($class).'"' : ' class="reicon"';
        $extra = '';

        foreach ($attrs as $attr => $value) {
            $extra .= ' '.$attr.'="'.e((string) $value).'"';
        }

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 24 24" fill="none"%s%s data-slot="icon">%s</svg>',
            $size,
            $size,
            $classAttr,
            $extra,
            $inner
        );
    }
}
