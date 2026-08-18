<div class="min-h-dvh mine-page-bg px-6 py-4 flex justify-center items-center">
    <div class="mine-card w-full max-w-120 flex flex-col justify-center p-6">
        <h1 class="text-center font-medium text-xl mine-text-primary mb-2">{{ __('Create your account') }}</h1>
        <p class="text-center text-sm mb-6 mine-text-secondary font-medium">{{ __('Enter your information to create your account') }}</p>

        <form wire:submit="register" class="flex flex-col">
            @if($register_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="{{ __('Too many sign-up attempts.') }}">{{ __('Please try again in a minute.') }}</x-mine.alert>
                </div>
            @endif
            @if($register_error === 'username_taken')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="{{ __('Duplicate username') }}">{{ __('That username is already in use.') }}</x-mine.alert>
                </div>
            @endif
            @if($register_error === 'email_taken')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="{{ __('Duplicate email') }}">{{ __('That email is already registered.') }}</x-mine.alert>
                </div>
            @endif

            <div class="mb-4">
                <x-mine.input wire:model="username" label="{{ __('Username') }}" placeholder="{{ __('Enter your username') }}" leftIcon="user" height="h-12">
                </x-mine.input>
            </div>

            <div class="mb-4">
                <x-mine.input wire:model="email" label="{{ __('Email address') }}" placeholder="{{ __('Enter your email address') }}" leftIcon="envelope" height="h-12">
                </x-mine.input>
            </div>

            <div class="mb-4">
                <x-mine.input wire:model="password" type="password" label="{{ __('Password') }}" placeholder="{{ __('Enter your password') }}" leftIcon="lock-closed" height="h-12">
                </x-mine.input>
            </div>

            <div class="mb-8">
                <x-mine.input wire:model="password_confirmation" type="password" label="{{ __('Confirm password') }}" placeholder="{{ __('Confirm your password') }}" leftIcon="lock-closed" height="h-12">
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
            <p class="text-sm mine-text-primary font-medium">{{ __('Already have an account?') }}</p>
            <a wire:navigate.hover href="{{ route('login') }}" class="text-sm mine-text-link font-medium">{{ __('Sign in ') }}</a>
        </div>
    </div>
</div>
