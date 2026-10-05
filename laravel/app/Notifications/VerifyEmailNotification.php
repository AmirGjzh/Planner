<?php

namespace App\Notifications;

use App\Notifications\Concerns\EmbedsThemeFonts;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

final class VerifyEmailNotification extends BaseVerifyEmail implements ShouldQueue
{
    use EmbedsThemeFonts, Queueable;

    /**
     * Build the mail representation of the notification on the project's own template, colored
     * with the app default theme's variables — a verification targets a brand-new account that
     * never chose one (only the reset mail follows the recipient's theme) — with the subset web
     * fonts embedded as data URIs (see embeddedFontUris()).
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject(__('Verify your email address'))
            ->action(__('Verify email'), $url)
            ->view('mail.verify-email', [
                'url' => $url,
                'expireMinutes' => (int) config('auth.verification.expire', 60),
                'themeVars' => $this->defaultThemeVars(),
                'fontUris' => $this->embeddedFontUris(),
            ]);
    }

    /**
     * The temporary signed verification URL, signed as a relative URL and rooted at the
     * configured APP_URL instead of the request Host: the route validates with
     * `signed:relative`, which HMACs the path and query only, so the link keeps
     * validating wherever APP_URL points and a forged Host header can never
     * poison the emailed link (`createUrlUsing` stays honored if ever set).
     *
     * @param  mixed  $notifiable
     */
    protected function verificationUrl($notifiable): string
    {
        if (self::$createUrlCallback !== null) {
            return call_user_func(self::$createUrlCallback, $notifiable);
        }

        return rtrim((string) config('app.url'), '/').URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes((int) config('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
            false,
        );
    }
}
