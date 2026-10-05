<?php

namespace App\Notifications\Concerns;

use Illuminate\Support\Facades\File;

/**
 * Shared mail-template data for the notifications that render the project's
 * own templates under resources/views/mail.
 */
trait EmbedsThemeFonts
{
    /**
     * The receiving user's theme variables for the template's `:root` block.
     *
     * @param  mixed  $notifiable
     * @return array<string, string>
     */
    protected function themeVarsFor($notifiable): array
    {
        return config('themes.vars.'.$notifiable->theme) ?? [];
    }

    /**
     * The app default theme's variables, for templates that must not follow the recipient's
     * account theme — a verification targets a brand-new account that never chose one.
     *
     * @return array<string, string>
     */
    protected function defaultThemeVars(): array
    {
        return config('themes.vars.'.config('themes.default')) ?? [];
    }

    /**
     * The subsetted Vazirmatn weights as data URIs, encoded when the mail renders so the sent
     * HTML is a self-contained static document: email HTML is never same-origin with the app,
     * so a served font URL gets CORS-blocked in clients like Mailpit and would need
     * per-environment URL/nginx/CORS setup. Subsetting keeps the payload under Gmail's ~102KB
     * message-clip budget — the size contract is pinned in the mail tests and regeneration is
     * documented in .ai/rules/auth.md.
     *
     * @return array{400: string, 500: string, 600: string} font weight => data URI
     */
    protected function embeddedFontUris(): array
    {
        $uris = [];

        foreach ([400 => 'Regular', 500 => 'Medium', 600 => 'SemiBold'] as $weight => $file) {
            $uris[$weight] = 'data:font/woff2;base64,'.base64_encode(
                File::get(resource_path("fonts/mail/Vazirmatn-{$file}.woff2"))
            );
        }

        return $uris;
    }
}
