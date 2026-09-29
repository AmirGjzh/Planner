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
     * Build the mail representation on the project's own template, colored
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
}
