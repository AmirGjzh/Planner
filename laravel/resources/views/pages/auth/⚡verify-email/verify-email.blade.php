<div class="mine-page-bg flex min-h-dvh items-center justify-center px-4 py-8 sm:px-6">
    <x-mine.animate as="div" delay="100" class="mine-card flex w-full max-w-120 flex-col justify-center p-6 sm:p-8">
        <h1 class="mine-text-primary mb-2 text-center text-xl font-semibold tracking-tight">
            {{ __('Verify your email address') }}
        </h1>

        <p class="mine-text-secondary mb-6 truncate text-center text-sm font-medium">
            {{ __('Open the verification link in your :email inbox', ['email' => $this->user->email]) }}
        </p>

        @if ($resend_error === 'rate_limited')
            <div class="mb-4">
                <x-mine.alert variant="warning" title="{{ __('Too many verification emails!') }}">
                    {{ __('Try again in a minute.') }}</x-mine.alert>
            </div>
        @endif

        <p class="mine-text-secondary mb-6 text-center text-sm font-medium">
            {{ __('The link expires in :count minutes. Don\'t forget to check your spam folder.', ['count' => (int) config('auth.verification.expire', 60)]) }}
        </p>

        <form wire:submit="resend">
            <div class="mt-2">
                <x-mine.button type="submit" height="h-12"> {{ __('Resend verification email') }}</x-mine.button>
            </div>
        </form>

        <div class="mt-5 flex items-center justify-center gap-2">
            <p class="mine-text-primary text-sm font-medium">{{ __('Not your account?') }}</p>
            <button
                type="button"
                x-data="{ busy: false }"
                x-on:click.prevent="
                    if (busy) return;
                    busy = true;
                    fetch('{{ route('logout') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                        .then((response) => {
                            if (! response.ok) throw new Error('Logout request failed');
                            window.location.assign('{{ route('home') }}');
                        })
                        .catch(() => busy = false);
                "
                class="mine-text-link inline-flex items-center py-1 text-sm font-medium cursor-pointer"
            >
                {{ __('Log out') }}
            </button>
        </div>
    </x-mine.animate>
</div>
