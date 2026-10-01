<div class="mine-page-bg flex min-h-dvh items-center justify-center px-4 py-8 sm:px-6">
    <x-mine.animate as="div" delay="100" class="mine-card flex w-full max-w-120 flex-col justify-center p-6 sm:p-8">
        <h1 class="mine-text-primary mb-2 text-center text-xl font-semibold tracking-tight">
            {{ __('Welcome back') }}
        </h1>
        <p class="mine-text-secondary mb-6 text-center text-sm font-medium">
            {{ __('Enter your information to sign in') }}
        </p>

        <form
            wire:submit="login"
            novalidate
            class="flex flex-col gap-4"
            x-data
            x-on:focus-first-error.window="
                $nextTick(() => $el.querySelector('[aria-invalid=&quot;true&quot;]')?.focus())
            "
        >
            @if ($login_error === 'rate_limited')
                <x-mine.alert variant="warning" title="{{ __('Too many sign-in attempts!') }}">
                    {{ __('Try again in a minute.') }}</x-mine.alert>
            @endif

            @if ($login_error === 'invalid')
                <x-mine.alert variant="danger" title="{{ __('Sign-in failed!') }}">
                    {{ __('Check your email and password, then try again.') }}</x-mine.alert>
            @endif

            <x-mine.input
                wire:model="email"
                label="{{ __('Email') }}"
                placeholder="{{ __('Your email') }}"
                leftIcon="Envelope2"
                height="h-12"
                type="email"
                spellcheck="false"
                autocomplete="email"
            >
            </x-mine.input>

            <x-mine.input
                wire:model="password"
                type="password"
                label="{{ __('Password') }}"
                placeholder="{{ __('Your password') }}"
                leftIcon="Lock"
                height="h-12"
                autocomplete="current-password"
            >
            </x-mine.input>

            <div class="flex items-center justify-between">
                <x-mine.checkbox wire:model="remember">{{ __('Remember me') }}</x-mine.checkbox>
                <a
                    wire:navigate.hover
                    href="{{ route('password.request') }}"
                    class="mine-text-link inline-flex items-center py-1 text-sm font-medium"
                >{{ __('Forgot password?') }}</a>
            </div>

            <x-mine.button wire:target="login" type="submit" height="h-12">{{ __('Sign in') }}</x-mine.button>
        </form>

        <div class="mt-4">
            <x-mine.separator label="{{ __('OR') }}"></x-mine.separator>
        </div>

        <div class="mt-2 flex items-center justify-center gap-2">
            <p class="mine-text-primary text-sm font-medium">{{ __('New to Planner?') }}</p>
            <a
                wire:navigate.hover
                href="{{ route('register') }}"
                class="mine-text-link inline-flex items-center py-1 text-sm font-medium"
            >{{ __('Create your account') }}</a>
        </div>
    </x-mine.animate>
</div>
