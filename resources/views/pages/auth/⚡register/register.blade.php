<div
    class="min-h-dvh flex items-center justify-center px-4 py-6 sm:px-6 lg:px-8 bg-gradient-to-r from-slate-300 to-slate-50">
    <x-ui.card size="md" class="w-full rounded-lg shadow-lg p-5 sm:p-6 md:p-8 bg-gradient-to-r from-white to-slate-100">
        <div class="flex justify-center mb-5">
            <x-ui.heading level="h1" size="lg">Create your account</x-ui.heading>
        </div>
        <form wire:submit="register" class="flex flex-col gap-6">
            <x-ui.field>
                <x-ui.label>Username</x-ui.label>
                <x-ui.input wire:model="user_name" type="text" placeholder="Username" leftIcon="user" />
                <x-ui.error name="user_name" />
            </x-ui.field>
            <x-ui.field>
                <x-ui.label>Email address</x-ui.label>
                <x-ui.input wire:model="email" type="text" placeholder="Email" leftIcon="envelope" />
                <x-ui.error name="email" />
            </x-ui.field>
            <x-ui.field>
                <x-ui.label>Password</x-ui.label>
                <x-ui.input wire:model="password" type="password" placeholder="Password" leftIcon="lock-closed"
                    revealable />
                <x-ui.error name="password" />
            </x-ui.field>
            <x-ui.field>
                <x-ui.label>Confirm Password</x-ui.label>
                <x-ui.input wire:model="password_confirmation" type="password" placeholder="Confirm Password" leftIcon="lock-closed"
                    revealable />
                <x-ui.error name="password_confirmation" />
            </x-ui.field>
            <x-ui.error name="register" class="" />
            <x-ui.button color="slate" type="submit"
                class="w-full rounded-lg bg-gradient-to-r from-slate-800 to-slate-600 mt-2">Sign up</x-ui.button>
        </form>
        <x-ui.text class="text-base text-center mt-5">Already have an account? <x-ui.link href="{{ route('login') }}" :primary="true"
                variant="ghost" class="text-sm" wire:navigate>Sign in</x-ui.link></x-ui.text>
    </x-ui.card>
</div>
