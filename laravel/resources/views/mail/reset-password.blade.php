<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'fa' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Reset your password') }}</title>
    <style>
        @font-face {
            font-family: 'Vazirmatn';
            font-style: normal;
            font-weight: 400;
            src: url('{{ $fontUris[400] }}') format('woff2');
        }

        @font-face {
            font-family: 'Vazirmatn';
            font-style: normal;
            font-weight: 500;
            src: url('{{ $fontUris[500] }}') format('woff2');
        }

        @font-face {
            font-family: 'Vazirmatn';
            font-style: normal;
            font-weight: 600;
            src: url('{{ $fontUris[600] }}') format('woff2');
        }

        :root {
            @foreach ($themeVars as $key => $value)
                --{{ $key }}: {{ $value }};
            @endforeach
            color-scheme: var(--color-scheme, light);
        }

        body {
            margin: 0;
            padding: 32px 12px;
            background-color: #f3f6fa;
            background: var(--mine-page-bg, #f3f6fa);
            font-family: 'Vazirmatn', Tahoma, 'Segoe UI', Arial, sans-serif;
            mso-generic-font-family: swiss;
            mso-font-alt: Tahoma;
        }

        .shell {
            width: 100%;
            max-width: 480px;
        }

        .card {
            background-color: #ffffff;
            background: var(--mine-card-bg, #ffffff);
            border: 1px solid var(--mine-card-border, #e2e8f0);
            border-radius: 16px;
            box-shadow:
                0 1px 2px rgba(15, 23, 42, .02),
                0 8px 18px rgba(15, 23, 42, .05),
                0 20px 40px rgba(15, 23, 42, .03);
        }

        .content {
            padding: 32px 28px;
            text-align: center;
        }

        .brand {
            margin: 0 0 20px;
            color: var(--mine-btn-primary-bg, #173b67);
        }

        h1 {
            margin: 0 0 12px;
            color: var(--mine-text-primary, #111827);
            font-family: 'Vazirmatn', Tahoma, 'Segoe UI', Arial, sans-serif;
            font-size: 22px;
            font-weight: 600;
            line-height: 1.35;
            mso-generic-font-family: swiss;
            mso-font-alt: Tahoma;
        }

        p {
            margin: 0 0 16px;
            color: var(--mine-text-secondary, #536174);
            font-family: 'Vazirmatn', Tahoma, 'Segoe UI', Arial, sans-serif;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.65;
            mso-generic-font-family: swiss;
            mso-font-alt: Tahoma;
        }

        .button {
            display: inline-block;
            margin: 8px 0 20px;
            padding: 14px 32px;
            border-radius: 12px;
            background-color: #173b67;
            background: var(--mine-btn-primary-bg, #173b67);
            color: var(--mine-btn-primary-text, #ffffff);
            font-family: 'Vazirmatn', Tahoma, 'Segoe UI', Arial, sans-serif;
            font-size: 15px;
            font-weight: 500;
            text-decoration: none;
            mso-generic-font-family: swiss;
            mso-font-alt: Tahoma;
        }

        .button:hover {
            background-color: #102f54;
            background: var(--mine-btn-primary-bg-hover, #102f54);
        }

        .hint {
            font-size: 14px;
        }

        .quiet {
            font-size: 14px;
        }
    </style>
</head>
<body>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table role="presentation" class="shell" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="card">
                            <div class="content">
                                <p class="brand"><x-mine.brand-logo width="100" height="20" translate="no" /></p>

                                <h1>{{ __('Reset your password') }}</h1>

                                <p>{{ __('You are receiving this email because we received a password reset request for your account.') }}</p>

                                <a class="button" href="{{ $url }}">{{ __('Reset Password') }}</a>

                                <p class="hint">{{ __('This password reset link will expire in :count minutes.', ['count' => $expireMinutes]) }}</p>

                                <p class="quiet">{{ __('If you didn\'t request a password reset, no further action is required.') }}</p>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
