<div class="mine-page-bg flex min-h-dvh items-center justify-center px-6 py-4">
    <x-mine.animate as="div" delay="100" class="mine-card flex w-full max-w-120 flex-col justify-center p-6">
        <h1 class="mine-text-primary mb-2 text-center text-xl font-semibold">{{ __('Welcome to Planner') }}</h1>
        <p class="mine-text-secondary mb-6 text-center text-sm font-medium">
            {{ __('Enter your information to create your account') }}
        </p>

        <form wire:submit="register" class="flex flex-col">
            @if ($register_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert
                        variant="warning"
                        title="{{ __('Too many sign-up attempts!') }}"
                    >
                        {{ __('Try again in a minute.') }}</x-mine.alert>
                </div>
            @endif
            @if ($register_error === 'username_taken')
                <div class="mb-4">
                    <x-mine.alert
                        variant="danger"
                        title="{{ __('Username taken!') }}"
                    >
                        {{ __('Try a different username.') }}</x-mine.alert>
                </div>
            @endif
            @if ($register_error === 'email_taken')
                <div class="mb-4">
                    <x-mine.alert
                        variant="danger"
                        title="{{ __('Email already registered!') }}"
                    >
                        {{ __('Sign in, or use another email.') }}</x-mine.alert>
                </div>
            @endif

            <div class="mb-4">
                <x-mine.input
                    wire:model="username"
                    label="{{ __('Username') }}"
                    placeholder="{{ __('Your username') }}"
                    leftIcon="User4"
                    height="h-12"
                >
                </x-mine.input>
            </div>

            <div class="mb-4">
                <x-mine.input
                    wire:model="email"
                    label="{{ __('Email') }}"
                    placeholder="{{ __('Your email') }}"
                    leftIcon="Envelope2"
                    height="h-12"
                >
                </x-mine.input>
            </div>

            <div class="mb-4">
                <x-mine.input
                    wire:model="password"
                    type="password"
                    label="{{ __('Password') }}"
                    placeholder="{{ __('Your password') }}"
                    leftIcon="Lock"
                    height="h-12"
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
                >
                </x-mine.input>
            </div>

            <div class="mb-4">
                <x-mine.button type="submit" height="h-12">{{ __('Sign up') }}</x-mine.button>
            </div>
        </form>

        <div class="mb-2 px-6">
            <x-mine.separator label="{{ __('OR') }}"></x-mine.separator>
        </div>

        <div class="flex justify-center gap-2">
            <p class="mine-text-primary text-sm font-medium">{{ __('Already have an account?') }}</p>
            <a
                wire:navigate.hover
                href="{{ route('login') }}"
                class="mine-text-link text-sm font-medium"
            >{{ __('Sign in') }}</a>
        </div>
    </x-mine.animate>
</div>
