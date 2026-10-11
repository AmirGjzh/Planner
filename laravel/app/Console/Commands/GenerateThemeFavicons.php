<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('themes:favicons')]
#[Description('Regenerate the per-theme favicon SVGs in public/favicons from the public/favicon.svg template')]
final class GenerateThemeFavicons extends Command
{
    public function handle(): int
    {
        $templatePath = public_path('favicon.svg');
        $template = is_file($templatePath) ? file_get_contents($templatePath) : false;

        if ($template === false || preg_match_all('/fill="#[0-9a-fA-F]{6}"/', $template) !== 1) {
            $this->error('public/favicon.svg must exist and carry exactly one hex fill to recolor.');

            return self::FAILURE;
        }

        $directory = public_path('favicons');

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            $this->error('Could not create public/favicons.');

            return self::FAILURE;
        }

        foreach (config('themes.vars') as $slug => $vars) {
            $color = $vars['mine-btn-primary-bg'] ?? null;

            if ($color === null) {
                $this->error("Theme [{$slug}] defines no mine-btn-primary-bg.");

                return self::FAILURE;
            }

            $written = file_put_contents(
                $directory.'/'.$slug.'.svg',
                preg_replace('/fill="#[0-9a-fA-F]{6}"/', "fill=\"{$color}\"", $template),
            );

            if ($written === false) {
                $this->error("Could not write public/favicons/{$slug}.svg.");

                return self::FAILURE;
            }

            $this->components->info("{$slug}: {$color}");
        }

        return self::SUCCESS;
    }
}
