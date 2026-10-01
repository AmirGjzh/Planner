<div class="mine-page-bg flex min-h-dvh items-center justify-center px-4 py-8 sm:px-6">
    <x-mine.animate as="div" delay="100" class="mine-card flex w-full max-w-120 flex-col justify-center p-6 sm:p-8">
        <h1 class="mine-text-primary mb-2 text-center text-xl font-semibold tracking-tight">
            {{ __('Choose a new password') }}
        </h1>

        @if ($email !== '')
            <p class="mine-text-secondary mb-6 truncate text-center text-sm font-medium">
                {{ __('Resetting password for :email', ['email' => $email]) }}
            </p>
        @else
            <p class="mine-text-secondary mb-6 text-center text-sm font-medium">
                {{ __('Enter and confirm your new password') }}
            </p>
        @endif

        @if ($reset_error === 'rate_limited')
            <div class="mb-4">
                <x-mine.alert variant="warning" title="{{ __('Too many reset attempts!') }}">
                    {{ __('Try again in a minute.') }}</x-mine.alert>
            </div>
        @endif

        @if ($reset_error === 'invalid_link')
            <div class="mb-4">
                <x-mine.alert variant="danger" title="{{ __('Link invalid or expired!') }}">
                    {{ __('This password reset link is invalid or has expired.') }}
                    <a
                        wire:navigate.hover
                        href="{{ route('password.request') }}"
                        class="mine-text-link inline-flex items-center py-1 font-bold"
                    >{{ __('Request a new link') }}</a>
                </x-mine.alert>
            </div>
        @endif

        @if ($reset_error === 'same_password')
            <div class="mb-4">
                <x-mine.alert variant="danger" title="{{ __('Password unchanged!') }}">
                    {{ __('Choose a password you haven\'t used before.') }}
                </x-mine.alert>
            </div>
        @endif

        @if ($token && $reset_error !== 'invalid_link')
            <form
                wire:submit="resetPassword"
                novalidate
                class="flex flex-col gap-4"
                x-data
                x-on:focus-first-error.window="
                    $nextTick(() => $el.querySelector('[aria-invalid=&quot;true&quot;]')?.focus())
                "
            >
                <x-mine.input
                    wire:model="password"
                    type="password"
                    label="{{ __('Password') }}"
                    placeholder="{{ __('Your password') }}"
                    leftIcon="Lock"
                    height="h-12"
                    autocomplete="new-password"
                >
                </x-mine.input>

                <x-mine.input
                    wire:model="password_confirmation"
                    type="password"
                    label="{{ __('Confirm password') }}"
                    placeholder="{{ __('Your password again') }}"
                    leftIcon="Lock"
                    height="h-12"
                    autocomplete="new-password"
                >
                </x-mine.input>

                <div class="mt-2">
                    <x-mine.button wire:target="resetPassword" type="submit" height="h-12">
                        {{ __('Reset password') }}</x-mine.button>
                </div>
            </form>
        @endif

        <div class="mt-4">
            <x-mine.separator label="{{ __('OR') }}"></x-mine.separator>
        </div>

        <div class="mt-2 flex items-center justify-center gap-2">
            <p class="mine-text-primary text-sm font-medium">{{ __('Remember your password?') }}</p>
            <a
                wire:navigate.hover
                href="{{ route('login') }}"
                class="mine-text-link inline-flex items-center py-1 text-sm font-medium"
            >{{ __('Sign in') }}</a>
        </div>
    </x-mine.animate>
</div>
