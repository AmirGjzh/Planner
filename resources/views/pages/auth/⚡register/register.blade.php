<div class="min-h-dvh mine-page-bg px-6 py-4 flex justify-center items-center">
    <div class="mine-card w-full max-w-120 flex flex-col justify-center p-6">
        <h1 class="text-center font-semibold text-xl mine-text-primary mb-2">{{ __('Welcome to Planner') }}</h1>
        <p class="text-center text-sm mb-6 mine-text-secondary font-medium">{{ __('Enter your details to create your account') }}</p>

        <form wire:submit="register" class="flex flex-col">
            @if($register_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="{{ __('Too many attempts') }}">{{ __('Try again in a minute.') }}</x-mine.alert>
                </div>
            @endif
            @if($register_error === 'username_taken')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="{{ __('Username taken') }}">{{ __('Try a different username.') }}</x-mine.alert>
                </div>
            @endif
            @if($register_error === 'email_taken')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="{{ __('Email already registered') }}">{{ __('Sign in, or use another email.') }}</x-mine.alert>
                </div>
            @endif

            <div class="mb-4">
                <x-mine.input wire:model="username" label="{{ __('Username') }}" placeholder="{{ __('Your username') }}" leftIcon="User4" height="h-12">
                </x-mine.input>
            </div>

            <div class="mb-4">
                <x-mine.input wire:model="email" label="{{ __('Email') }}" placeholder="{{ __('Your email') }}" leftIcon="Envelope2" height="h-12">
                </x-mine.input>
            </div>

            <div class="mb-4">
                <x-mine.input wire:model="password" type="password" label="{{ __('Password') }}" placeholder="{{ __('Your password') }}" leftIcon="Lock" height="h-12">
                </x-mine.input>
            </div>

            <div class="mb-8">
                <x-mine.input wire:model="password_confirmation" type="password" label="{{ __('Confirm password') }}" placeholder="{{ __('Your password again') }}" leftIcon="Lock" height="h-12">
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
            <a wire:navigate.hover href="{{ route('login') }}" class="text-sm mine-text-link font-medium">{{ __('Sign in') }}</a>
        </div>
    </div>
</div>
