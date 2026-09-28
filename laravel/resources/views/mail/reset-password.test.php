<?php

use App\Models\User;
use App\Notifications\ResetPasswordNotification;

it('renders the themed reset mail with the emailed url', function () {
    $user = User::factory()->create(['email' => 'amir@example.com', 'theme' => 'ocean']);
    $mail = (new ResetPasswordNotification('token-123'))->toMail($user);

    $html = view($mail->view, $mail->viewData)->render();

    expect($mail->view)->toBe('mail.reset-password')
        ->and($mail->viewData['url'])->toBe($mail->actionUrl)
        ->and($html)
        ->toContain('href="'.e($mail->viewData['url']).'"')
        ->toContain('Reset your password')
        ->toContain('Reset Password')
        ->toContain('This password reset link will expire in 60 minutes.')
        ->toContain('--mine-btn-primary-bg')
        ->toContain('dir="ltr"');
});

it('renders the Persian copy with rtl direction', function () {
    app()->setLocale('fa');

    $user = User::factory()->create(['email' => 'amir@example.com', 'theme' => 'ocean']);
    $mail = (new ResetPasswordNotification('token-123'))->toMail($user);

    $html = view($mail->view, $mail->viewData)->render();

    expect($html)
        ->toContain('dir="rtl"')
        ->toContain('بازنشانی رمز عبور')
        ->toContain('تا 60 دقیقه');
});
