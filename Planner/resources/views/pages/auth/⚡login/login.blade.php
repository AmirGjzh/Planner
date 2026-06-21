<div
    class="min-h-dvh flex items-center justify-center px-4 py-6 sm:px-6 lg:px-8 bg-gradient-to-r from-slate-300 to-slate-50">
    <x-ui.card size="md" class="w-full rounded-lg shadow-lg p-5 sm:p-6 md:p-8 bg-gradient-to-r from-white to-slate-100">
        <div class="flex justify-center mb-5">
            <x-ui.heading level="h1" size="lg">Sign in to your account</x-ui.heading>
        </div>
        <form wire:submit="login" class="flex flex-col gap-6">
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
            <div class="flex justify-between w-full">
                <x-ui.checkbox size="sm" wire:model="remember" label="Remember me"></x-ui.checkbox>
                <x-ui.link href="" :primary="true" variant="ghost" class="text-sm">Forgot password?</x-ui.link>
            </div>
            <x-ui.error name="login" class="" />
            <x-ui.button color="slate" type="submit"
                class="w-full rounded-lg bg-gradient-to-r from-slate-800 to-slate-600">Sign in</x-ui.button>
        </form>
        <x-ui.text class="text-base text-center mt-5">Don't have an account? <x-ui.link href="" :primary="true"
                variant="ghost" class="text-sm">Sign up</x-ui.link></x-ui.text>
    </x-ui.card>
</div>
