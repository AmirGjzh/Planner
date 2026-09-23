<div class="mine-page-bg flex min-h-dvh items-center justify-center px-6 py-4">
    <x-mine.animate as="div" delay="100" class="mine-card flex w-full max-w-120 flex-col justify-center p-6">
        <h1 class="mine-text-primary mb-2 text-center text-xl font-semibold">{{ __('Welcome back') }}</h1>
        <p class="mine-text-secondary mb-6 text-center text-sm font-medium">
            {{ __('Enter your information to sign in') }}
        </p>

        <form wire:submit="login" class="flex flex-col">
            @if ($login_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert
                        variant="warning"
                        title="{{ __('Too many sign-in attempts!') }}"
                    >
                        {{ __('Try again in a minute.') }}</x-mine.alert>
                </div>
            @endif

            @if ($login_error === 'invalid')
                <div class="mb-4">
                    <x-mine.alert
                        variant="danger"
                        title="{{ __('Sign-in failed!') }}"
                    >
                        {{ __('The email or password you entered is incorrect.') }}</x-mine.alert>
                </div>
            @endif

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

            <div class="mb-6">
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

            <div class="mb-5 flex justify-between">
                <x-mine.checkbox wire:model="remember">{{ __('Remember me') }}</x-mine.checkbox>
                <a href="#" class="mine-text-link text-sm font-medium">{{ __('Forgot password?') }}</a>
            </div>

            <div class="mb-4">
                <x-mine.button wire:target="login" type="submit" height="h-12">{{ __('Sign in') }}</x-mine.button>
            </div>
        </form>

        <div class="mb-2 px-6">
            <x-mine.separator label="{{ __('OR') }}"></x-mine.separator>
        </div>

        <div class="flex justify-center gap-2">
            <p class="mine-text-primary text-sm font-medium">{{ __('New to Planner?') }}</p>
            <a
                wire:navigate.hover
                href="{{ route('register') }}"
                class="mine-text-link text-sm font-medium"
            >{{ __('Create your account') }}</a>
        </div>
    </x-mine.animate>
</div>
