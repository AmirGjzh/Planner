<?php

use Illuminate\Support\Facades\File;

it('keeps fa.context.md in exact parity with fa.json', function () {
    $keys = array_keys(json_decode(File::get(lang_path('fa.json')), true, 512, JSON_THROW_ON_ERROR));

    // Annotation lines are the ones starting with the JSON-quoted key:
    //   "Sign in" → button · login submit
    $pattern = '/^("(?:[^"\\\\]|\\\\.)*")\s*→\s*(\S.*)$/u';
    $annotated = [];
    $unparsable = [];

    foreach (File::lines(lang_path('fa.context.md')) as $line) {
        if (! str_starts_with($line, '"')) {
            continue;
        }

        if (! preg_match($pattern, $line, $matches)) {
            $unparsable[] = $line;

            continue;
        }

        $annotated[] = [
            json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR),
            trim($matches[2]),
        ];
    }

    // Same keys, same order, one annotation line per key — both directions.
    expect($unparsable)->toBeEmpty()
        ->and(array_column($annotated, 0))->toBe($keys);

    foreach ($annotated as [$key, $role]) {
        expect($role)->not->toBeEmpty()
            ->and($role)->not->toContain('!! TBD !!');
    }
});
