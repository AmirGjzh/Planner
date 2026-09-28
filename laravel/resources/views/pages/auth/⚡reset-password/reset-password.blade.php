<div class="mine-page-bg flex min-h-dvh items-center justify-center px-6 py-4">
    <x-mine.animate as="div" delay="100" class="mine-card flex w-full max-w-120 flex-col justify-center p-6">
        <h1 class="mine-text-primary mb-2 text-center text-xl font-semibold">{{ __('Choose a new password') }}</h1>

        @if ($email !== '')
            <div class="mb-6">
                <x-mine.alert variant="info" title="{{ __('Enter and confirm your new password') }}">
                    {{ __('Resetting password for :email', ['email' => $email]) }}
                </x-mine.alert>
            </div>
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
                        class="mine-text-link font-medium"
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

        @if ($token)
            <form wire:submit="resetPassword" class="flex flex-col">
                <div class="mb-4">
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
                </div>

                <div class="mb-8">
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
                </div>

                <div class="mb-4">
                    <x-mine.button wire:target="resetPassword" type="submit" height="h-12">
                        {{ __('Reset password') }}</x-mine.button>
                </div>
            </form>
        @endif

        <div class="mb-2 px-6">
            <x-mine.separator label="{{ __('OR') }}"></x-mine.separator>
        </div>

        <div class="flex justify-center gap-2">
            <p class="mine-text-primary text-sm font-medium">{{ __('Remember your password?') }}</p>
            <a
                wire:navigate.hover
                href="{{ route('login') }}"
                class="mine-text-link text-sm font-medium"
            >{{ __('Sign in') }}</a>
        </div>
    </x-mine.animate>
</div>
