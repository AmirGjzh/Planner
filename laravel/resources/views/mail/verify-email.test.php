<?php

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

it('renders the verification mail on the app default theme with the signed url', function () {
    $user = User::factory()->create(['email' => 'amir@example.com', 'theme' => 'ocean']);
    $mail = (new VerifyEmailNotification)->toMail($user);

    $html = view($mail->view, $mail->viewData)->render();

    $parts = parse_url($mail->actionUrl);
    parse_str($parts['query'] ?? '', $query);

    expect($mail->view)->toBe('mail.verify-email')
        ->and($mail->viewData['url'])->toBe($mail->actionUrl)
        ->and($mail->viewData['themeVars'])->toBe(config('themes.vars.'.config('themes.default')))
        ->and($html)
        ->toContain('href="'.e($mail->viewData['url']).'"')
        ->toContain('Verify your email address')
        ->toContain('Verify email')
        ->toContain('This verification link will expire in 60 minutes.')
        ->toContain('--mine-btn-primary-bg')
        ->toContain('<svg')                                // brand ships as the inline logo SVG…
        ->toContain('color: var(--mine-btn-primary-bg')    // …tinted by the app default theme, never the account's
        ->toContain('dir="ltr"');

    expect($parts['path'] ?? null)->toMatch('/^\/email\/verify\/\d+\/[0-9a-f]{40}$/')
        ->and($query)->toHaveKeys(['expires', 'signature']);
});

it('renders the Persian copy with rtl direction', function () {
    app()->setLocale('fa');

    $user = User::factory()->create(['email' => 'amir@example.com', 'theme' => 'ocean']);
    $mail = (new VerifyEmailNotification)->toMail($user);

    $html = view($mail->view, $mail->viewData)->render();

    expect($html)
        ->toContain('dir="rtl"')
        ->toContain('ایمیلت رو تایید کن')
        ->toContain('تا 60 دقیقه');
});

it('sends the notification in the user saved locale', function () {
    $user = User::factory()->create(['email' => 'amir@example.com', 'locale' => 'fa']);

    $user->notify(new VerifyEmailNotification);

    $message = Mail::mailer()->getSymfonyTransport()->messages()->last()->getOriginalMessage();

    expect($message->getSubject())->toBe('ایمیلت رو تایید کن')
        ->and($message->getHtmlBody())->toContain('dir="rtl"');
});

it('queues the verification notification for background delivery', function () {
    Queue::fake();
    $user = User::factory()->create(['email' => 'amir@example.com']);

    $user->notify(new VerifyEmailNotification);

    Queue::assertPushed(SendQueuedNotifications::class);
});

it('roots the verification link at APP_URL even when the request host is forged', function () {
    $this->app->instance('request', Request::create('http://evil.example/register'));

    $user = User::factory()->create(['email' => 'amir@example.com']);
    $mail = (new VerifyEmailNotification)->toMail($user);

    expect($mail->actionUrl)
        ->toStartWith(rtrim(config('app.url'), '/').'/email/verify')
        ->not->toStartWith('http://evil.example');
});

it('embeds the subset fonts as data uris and stays under the gmail clip budget', function () {
    $user = User::factory()->create(['email' => 'amir@example.com']);
    $mail = (new VerifyEmailNotification)->toMail($user);

    $html = view($mail->view, $mail->viewData)->render();

    expect(substr_count($html, 'data:font/woff2;base64,'))->toBe(3)
        ->and($html)->not->toContain('/fonts/Vazirmatn')
        ->and(strlen($html))->toBeLessThan(100 * 1024);
});
