<div class="min-h-dvh bg-page px-6 py-4 flex justify-center items-center">
    <div class="card w-full max-w-120 flex flex-col justify-center p-6">
        <h1 class="text-center font-medium text-xl text-primary mb-2">Create your account</h1>
        <p class="text-center text-sm mb-6 text-secondary font-medium">Let's get you started</p>

        <form wire:submit="register" class="flex flex-col">
            @if($register_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="Too many sign up attempts.">Please try again in a minute.</x-mine.alert>
                </div>
            @endif
            @if($register_error === 'username_taken')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="Username already taken.">That username is already in use.</x-mine.alert>
                </div>
            @endif
            @if($register_error === 'email_taken')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="Email already taken.">That email is already registered.</x-mine.alert>
                </div>
            @endif

            <div class="mb-4">
                <x-mine.input wire:model="username" label="Username" placeholder="Username" leftIcon="user" height="h-12">
                </x-mine.input>
            </div>

            <div class="mb-4">
                <x-mine.input wire:model="email" label="Email Address" placeholder="Email" leftIcon="envelope" height="h-12">
                </x-mine.input>
            </div>

            <div class="mb-4">
                <x-mine.input wire:model="password" type="password" label="Password" placeholder="Password" leftIcon="lock-closed" height="h-12">
                </x-mine.input>
            </div>

            <div class="mb-8">
                <x-mine.input wire:model="password_confirmation" type="password" label="Confirm Password" placeholder="Confirm Password" leftIcon="lock-closed" height="h-12">
                </x-mine.input>
            </div>

            <div class="mb-4">
                <x-mine.button type="submit" height="h-12">Sign up</x-mine.button>
            </div>
        </form>

        <div class="mb-2 px-6">
            <x-mine.separator label="OR"></x-mine.separator>
        </div>

        <div class="flex justify-center">
            <p class="text-sm text-primary font-medium mr-2">Already have an account?</p>
            <a wire:navigate.hover href="{{ route('login') }}" class="text-sm text-(--mine-text-link) font-medium">Sign in</a>
        </div>
    </div>
</div>
