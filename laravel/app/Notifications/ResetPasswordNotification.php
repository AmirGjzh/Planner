<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

final class ResetPasswordNotification extends BaseResetPassword implements ShouldQueue
{
    use Queueable;

    /**
     * Build the mail representation of the notification on the project's own template, colored
     * with the receiving user's theme variables.
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
            ]);
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
