<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\File;

final class ResetPasswordNotification extends BaseResetPassword implements ShouldQueue
{
    use Queueable;

    /**
     * Build the mail representation of the notification on the project's own template, colored
     * with the receiving user's theme variables, with the subset web fonts embedded as data
     * URIs (see embeddedFontUris()).
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);
        $theme = $notifiable->theme;

        return (new MailMessage)
            ->subject(__('Reset your password'))
            ->action(__('Reset Password'), $url)
            ->view('mail.reset-password', [
                'url' => $url,
                'expireMinutes' => (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
                'themeVars' => config("themes.vars.$theme") ?? [],
                'fontUris' => $this->embeddedFontUris(),
            ]);
    }

    /**
     * The subsetted Vazirmatn weights as data URIs, encoded when the mail renders so the sent
     * HTML is a self-contained static document: email HTML is never same-origin with the app,
     * so a served font URL gets CORS-blocked in clients like Mailpit and would need
     * per-environment URL/nginx/CORS setup. Subsetting keeps the payload under Gmail's ~102KB
     * message-clip budget — the size contract is pinned in the mail test and regeneration is
     * documented in .ai/rules/auth.md.
     *
     * @return array{400: string, 500: string, 600: string} font weight => data URI
     */
    private function embeddedFontUris(): array
    {
        $uris = [];

        foreach ([400 => 'Regular', 500 => 'Medium', 600 => 'SemiBold'] as $weight => $file) {
            $uris[$weight] = 'data:font/woff2;base64,'.base64_encode(
                File::get(resource_path("fonts/mail/Vazirmatn-{$file}.woff2"))
            );
        }

        return $uris;
    }

    /**
     * Root the link at the configured APP_URL instead of the request Host,
     * so a forged Host header can never poison the emailed reset link.
     *
     * @param  mixed  $notifiable
     */
    protected function resetUrl($notifiable): string
    {
        if (self::$createUrlCallback !== null) {
            return call_user_func(self::$createUrlCallback, $notifiable, $this->token);
        }

        return rtrim((string) config('app.url'), '/').route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false);
    }
}
