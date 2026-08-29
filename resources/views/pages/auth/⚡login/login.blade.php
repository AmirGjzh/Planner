<div class="min-h-dvh mine-page-bg px-6 py-4 flex justify-center items-center">
    <div class="mine-card w-full max-w-120 flex flex-col justify-center p-6">
        <h1 class="text-center font-semibold text-xl mine-text-primary mb-2">{{ __('Welcome back') }}</h1>
        <p class="text-center text-sm mb-6 mine-text-secondary font-medium">{{ __('Enter your details to sign in') }}</p>

        <form wire:submit="login" class="flex flex-col">
            @if($login_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="{{ __('Too many attempts') }}">{{ __('Try again in a minute') }}</x-mine.alert>
                </div>
            @endif

            @if($login_error === 'invalid')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="{{ __('Sign-in failed') }}">{{ __('The email or password you entered is incorrect') }}</x-mine.alert>
                </div>
            @endif

            <div class="mb-4">
                <x-mine.input wire:model="email" label="{{ __('Email') }}" placeholder="{{ __('Your email') }}" leftIcon="Envelope2" height="h-12">
                </x-mine.input>
            </div>

            <div class="mb-6">
                <x-mine.input wire:model="password" type="password" label="{{ __('Password') }}" placeholder="{{ __('Your password') }}" leftIcon="Lock" height="h-12">
                </x-mine.input>
            </div>

            <div class="flex justify-between mb-5">
                <x-mine.checkbox wire:model="remember">{{ __('Remember me') }}</x-mine.checkbox>
                <a href="#" class="text-sm mine-text-link font-medium">{{ __('Forgot password?') }}</a>
            </div>

            <div class="mb-4">
                <x-mine.button type="submit" height="h-12">{{ __('Sign in') }}</x-mine.button>
            </div>
        </form>

        <div class="mb-2 px-6">
            <x-mine.separator label="{{ __('OR') }}"></x-mine.separator>
        </div>

        <div class="flex justify-center gap-2">
            <p class="text-sm mine-text-primary font-medium">{{ __('New to Planner?') }}</p>
            <a wire:navigate.hover href="{{ route('register') }}" class="text-sm mine-text-link font-medium">{{ __('Create your account') }}</a>
        </div>
    </div>
</div>
