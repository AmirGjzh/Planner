<div class="mine-page-bg flex min-h-dvh items-center justify-center px-6 py-4">
    <x-mine.animate as="div" delay="100" class="mine-card flex w-full max-w-120 flex-col justify-center p-6">
        @if ($sent)
            <h1 class="mine-text-primary mb-2 text-center text-xl font-semibold">{{ __('Check your email') }}</h1>
            <p class="mine-text-secondary mb-6 text-center text-sm font-medium">
                {{ __('If an account exists for :email, a reset link has been sent.', ['email' => $email]) }}
            </p>

            <div class="mb-4">
                <x-mine.alert variant="success" title="{{ __('Check your inbox') }}">
                    {{ __('The link expires in :count minutes. Don\'t forget to check your spam folder.', ['count' => $this->expireMinutes]) }}
                </x-mine.alert>
            </div>

            <div class="mb-4">
                <x-mine.button type="button" wire:click="restart" height="h-12">
                    {{ __('Use a different email') }}</x-mine.button>
            </div>

            <div class="mb-2 px-6">
                <x-mine.separator label="{{ __('OR') }}"></x-mine.separator>
            </div>

            <div class="flex justify-center gap-2">
                <p class="mine-text-primary text-sm font-medium">{{ __('Remember your password?') }}</p>
                <a
                    wire:navigate.hover
                    href="{{ route('login') }}"
                    class="mine-text-link text-sm font-medium"
                >{{ __('Sign in') }}</a>
            </div>
        @else
            <h1 class="mine-text-primary mb-2 text-center text-xl font-semibold">{{ __('Reset your password') }}</h1>
            <p class="mine-text-secondary mb-6 text-center text-sm font-medium">
                {{ __('Enter your email to receive a reset link') }}
            </p>

            <form wire:submit="send" class="flex flex-col">
                @if ($send_error === 'rate_limited')
                    <div class="mb-4">
                        <x-mine.alert variant="warning" title="{{ __('Too many reset requests!') }}">
                            {{ __('Try again in a minute.') }}</x-mine.alert>
                    </div>
                @endif

                <div class="mb-6">
                    <x-mine.input
                        wire:model="email"
                        label="{{ __('Email') }}"
                        placeholder="{{ __('Your email') }}"
                        leftIcon="Envelope2"
                        height="h-12"
                        autocomplete="email"
                    >
                    </x-mine.input>
                </div>

                <div class="mb-4">
                    <x-mine.button wire:target="send" type="submit" height="h-12">
                        {{ __('Send reset link') }}</x-mine.button>
                </div>
            </form>

            <div class="mb-2 px-6">
                <x-mine.separator label="{{ __('OR') }}"></x-mine.separator>
            </div>

            <div class="flex justify-center gap-2">
                <p class="mine-text-primary text-sm font-medium">{{ __('Remember your password?') }}</p>
                <a
                    wire:navigate.hover
                    href="{{ route('login') }}"
                    class="mine-text-link text-sm font-medium"
                >{{ __('Sign in') }}</a>
            </div>
        @endif
    </x-mine.animate>
</div>
