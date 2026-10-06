<div class="mine-page-bg flex min-h-dvh items-center justify-center px-4 py-8 sm:px-6">
    <x-mine.animate as="div" delay="100" class="mine-card flex w-full max-w-120 flex-col justify-center p-6 sm:p-8">
        <h1 class="mine-text-primary mb-2 text-center text-xl font-semibold tracking-tight">
            {{ __('Welcome to Planner') }}
        </h1>
        <p class="mine-text-secondary mb-6 text-center text-sm font-medium">
            {{ __('Enter your information to create your account') }}
        </p>

        <form
            wire:submit="register"
            novalidate
            class="flex flex-col gap-4"
            x-data
            x-on:focus-first-error.window="
                $nextTick(() => $el.querySelector('[aria-invalid=&quot;true&quot;]')?.focus())
            "
        >
            @if ($register_error === 'rate_limited')
                <x-mine.alert variant="warning" title="{{ __('Too many sign-up attempts!') }}">
                    {{ __('Try again in a minute.') }}</x-mine.alert>
            @endif
            @if ($register_error === 'username_taken')
                <x-mine.alert variant="danger" title="{{ __('Username taken!') }}">
                    {{ __('Try a different username.') }}</x-mine.alert>
            @endif
            @if ($register_error === 'email_taken')
                <x-mine.alert variant="danger" title="{{ __('Email already registered!') }}">
                    {{ __('Sign in, or use another email.') }}</x-mine.alert>
            @endif

            <x-mine.input
                wire:model="username"
                label="{{ __('Username') }}"
                placeholder="{{ __('Your username') }}"
                leftIcon="User4"
                height="h-12"
            >
            </x-mine.input>

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
                <x-mine.button wire:target="register" type="submit" height="h-12">{{ __('Sign up') }}</x-mine.button>
            </div>
        </form>

        <div class="mt-5 flex items-center justify-center gap-2">
            <p class="mine-text-primary text-sm font-medium">{{ __('Already have an account?') }}</p>
            <a
                wire:navigate.hover
                href="{{ route('login') }}"
                class="mine-text-link inline-flex items-center py-1 text-sm font-medium"
            >{{ __('Sign in') }}</a>
        </div>
    </x-mine.animate>
</div>
