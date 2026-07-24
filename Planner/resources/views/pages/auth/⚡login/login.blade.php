<div class="min-h-dvh mine-page-bg px-6 py-4 flex justify-center items-center">
    <div class="mine-card w-full max-w-120 flex flex-col justify-center p-6">
        <h1 class="text-center font-medium text-xl mine-text-primary mb-2">Welcome back</h1>
        <p class="text-center text-sm mb-6 mine-text-secondary font-medium">Sign in to continue to your workspace</p>

        <form wire:submit="login" class="flex flex-col">
            @if($login_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="Too many sign in attempts.">Please try again in a minute.</x-mine.alert>
                </div>
            @endif

            @if($login_error === 'invalid')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="Invalid credentials.">The email or password you entered is incorrect.</x-mine.alert>
                </div>
            @endif

            <div class="mb-4">
                <x-mine.input wire:model="email" label="Email Address" placeholder="Email Address" leftIcon="envelope" height="h-12">
                </x-mine.input>
            </div>

            <div class="mb-6">
                <x-mine.input wire:model="password" type="password" label="Password" placeholder="Password" leftIcon="lock-closed" height="h-12">
                </x-mine.input>
            </div>

            <div class="flex justify-between mb-6">
                <x-mine.checkbox wire:model="remember">Remember me</x-mine.checkbox>
                <a href="#" class="text-sm mine-text-link font-medium">Forgot password?</a>
            </div>

            <div class="mb-4">
                <x-mine.button type="submit" height="h-12">Sign in</x-mine.button>
            </div>
        </form>

        <div class="mb-2 px-6">
            <x-mine.separator label="OR"></x-mine.separator>
        </div>

        <div class="flex justify-center">
            <p class="text-sm mine-text-primary font-medium mr-2">Don't have an account?</p>
            <a wire:navigate.hover href="{{ route('register') }}" class="text-sm mine-text-link font-medium">Create account</a>
        </div>
    </div>
</div>
