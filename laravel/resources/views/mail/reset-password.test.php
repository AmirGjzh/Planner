<?php

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

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
        ->toContain('<svg')                                // brand ships as the inline logo SVG…
        ->toContain('color: var(--mine-btn-primary-bg')    // …tinted by the receiving user's theme
        ->toContain('dir="ltr"');
});

it('renders the Persian copy with rtl direction', function () {
    app()->setLocale('fa');

    $user = User::factory()->create(['email' => 'amir@example.com', 'theme' => 'ocean']);
    $mail = (new ResetPasswordNotification('token-123'))->toMail($user);

    $html = view($mail->view, $mail->viewData)->render();

    expect($html)
        ->toContain('dir="rtl"')
        ->toContain('بازیابی رمز عبور')
        ->toContain('این لینک بازیابی 60 دقیقه');
});

it('sends the notification in the user saved locale', function () {
    $user = User::factory()->create(['email' => 'amir@example.com', 'locale' => 'fa']);

    $user->notify(new ResetPasswordNotification('token-123'));

    $message = Mail::mailer()->getSymfonyTransport()->messages()->last()->getOriginalMessage();

    expect($message->getSubject())->toBe('بازیابی رمز عبور')
        ->and($message->getHtmlBody())->toContain('dir="rtl"');
});

it('queues the reset notification for background delivery', function () {
    Queue::fake();
    $user = User::factory()->create(['email' => 'amir@example.com']);

    $user->notify(new ResetPasswordNotification('token-123'));

    Queue::assertPushed(SendQueuedNotifications::class);
});

it('roots the reset link at APP_URL even when the request host is forged', function () {
    $this->app->instance('request', Request::create('http://evil.example/forgot-password'));

    $user = User::factory()->create(['email' => 'amir@example.com']);
    $mail = (new ResetPasswordNotification('token-123'))->toMail($user);

    expect($mail->actionUrl)
        ->toStartWith(rtrim(config('app.url'), '/').'/reset-password')
        ->not->toStartWith('http://evil.example');
});

it('embeds the subset fonts as data uris and stays under the gmail clip budget', function () {
    $user = User::factory()->create(['email' => 'amir@example.com']);
    $mail = (new ResetPasswordNotification('token-123'))->toMail($user);

    $html = view($mail->view, $mail->viewData)->render();

    expect(substr_count($html, 'data:font/woff2;base64,'))->toBe(3)
        ->and($html)->not->toContain('/fonts/Vazirmatn')
        ->and(strlen($html))->toBeLessThan(100 * 1024);
});
