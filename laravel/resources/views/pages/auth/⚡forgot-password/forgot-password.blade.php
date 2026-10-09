<div class="mine-page-bg flex min-h-dvh items-center justify-center px-4 py-8 sm:px-6">
    <x-mine.animate as="div" delay="100" class="mine-card flex w-full max-w-120 flex-col justify-center p-6 sm:p-8">
        @if ($sent)
            <h1 class="mine-text-primary mb-2 text-center text-xl font-semibold tracking-tight">
                {{ __('Check your email') }}
            </h1>
            <p class="mine-text-secondary mb-6 text-center text-sm font-medium">
                {!!
                    __(
                        'If an account exists for :email, a reset link has been sent.',
                        ['email' => '<span class="inline-block max-w-full truncate align-bottom">'.e($email).'</span>'],
                    )
                !!}
            </p>

            <div class="flex flex-col gap-4">
                <x-mine.alert variant="success" title="{{ __('Check your inbox!') }}">
                    {{ __('The link expires in :count minutes, don\'t forget to check your spam folder.', ['count' => $this->expireMinutes]) }}
                </x-mine.alert>

                <div class="mt-2">
                    <x-mine.button type="button" wire:click="restart" height="h-12">
                        {{ __('Use a different email') }}</x-mine.button>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-center gap-2">
                <p class="mine-text-primary text-sm font-medium">{{ __('Remember your password?') }}</p>
                <a
                    wire:navigate.hover
                    href="{{ route('login') }}"
                    class="mine-text-link inline-flex items-center py-1 text-sm font-medium"
                >{{ __('Sign in') }}</a>
            </div>
        @else
            <h1 class="mine-text-primary mb-2 text-center text-xl font-semibold tracking-tight">
                {{ __('Reset your password') }}
            </h1>
            <p class="mine-text-secondary mb-6 text-center text-sm font-medium">
                {{ __('Enter your email to receive a reset link') }}
            </p>

            <form
                wire:submit="send"
                novalidate
                class="flex flex-col gap-4"
                x-data
                x-on:focus-first-error.window="
                    $nextTick(() => $el.querySelector('[aria-invalid=&quot;true&quot;]')?.focus())
                "
            >
                @if ($send_error === 'rate_limited')
                    <x-mine.alert variant="warning" title="{{ __('Too many password-reset requests!') }}">
                        {{ __('Try again in a minute.') }}</x-mine.alert>
                @endif

                <x-mine.input
                    wire:model="email"
                    label="{{ __('Email') }}"
                    placeholder="{{ __('Your email') }}"
                    leftIcon="Envelope2"
                    height="h-12"
                    type="email"
                    spellcheck="false"
                    autocomplete="email"
                >
                </x-mine.input>

                <div class="mt-2">
                    <x-mine.button wire:target="send" type="submit" height="h-12">
                        {{ __('Send reset link') }}</x-mine.button>
                </div>
            </form>

            <div class="mt-5 flex items-center justify-center gap-2">
                <p class="mine-text-primary text-sm font-medium">{{ __('Remember your password?') }}</p>
                <a
                    wire:navigate.hover
                    href="{{ route('login') }}"
                    class="mine-text-link inline-flex items-center py-1 text-sm font-medium"
                >{{ __('Sign in') }}</a>
            </div>
        @endif
    </x-mine.animate>
</div>
