<div class="min-h-dvh bg-(--mine-page-bg-green) px-6 py-4 flex justify-center items-center">
    <div class="bg-(--mine-card-bg) w-full max-w-120 flex flex-col justify-center p-6 rounded-xl shadow-[0_10px_30px_rgba(16,24,40,0.1)]">
        <h1 class="text-center font-medium text-xl text-(--mine-text-primary) mb-2">Welcome back</h1>
        <p class="text-center text-sm mb-6 text-(--mine-text-secondary) font-medium">Sign in to continue to your workspace</p>

        @if($login_error === 'rate_limited')
            <div class="mb-4">
                <x-mine.alert variant="warning" title="Login is currently limited.">Please try again in a minute.</x-mine.alert>
            </div>
        @endif

        @if($login_error === 'invalid')
            <div class="mb-4">
                <x-mine.alert variant="danger" title="Invalid credentials.">The email or password you entered is incorrect.</x-mine.alert>
            </div>
        @endif

        <form wire:submit="login" class="flex flex-col">
            <div class="mb-4">
                <x-mine.input wire:model="email" label="Email Address" placeholder="Email" leftIcon="envelope">
                </x-mine.input>
            </div>

            <div class="mb-6">
                <x-mine.input wire:model="password" type="password" label="Password" placeholder="Password" leftIcon="lock-closed">
                </x-mine.input>
            </div>

            <div class="flex justify-between mb-6">
                <x-mine.checkbox wire:model="remember">Remember me</x-mine.checkbox>
                <a href="#" class="text-sm text-(--mine-text-link) font-medium">Forgot password?</a>
            </div>

            <div class="mb-4">
                <x-mine.button type="submit">Sign in</x-mine.button>
            </div>
        </form>

        <div class="mb-2 px-6">
            <x-mine.separator label="OR"></x-mine.separator>
        </div>

        <div class="flex justify-center">
            <p class="text-sm text-(--mine-text-primary) font-medium mr-2">Don't have an account?</p>
            <a wire:navigate href="{{ route('register') }}" class="text-sm text-(--mine-text-link) font-medium">Create account</a>
        </div>
    </div>
</div>
