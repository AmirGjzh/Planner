<?php

use App\Actions\Auth\ResendVerificationAction;
use App\Enums\ResendVerificationResult;
use App\Livewire\Concerns\HasUser;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth', ['title' => 'Verify your email address'])] class extends Component
{
    use HasUser;

    public ?string $resend_error = null;

    public function mount(): void
    {
        if ($this->user->hasVerifiedEmail()) {
            $this->redirectRoute('dashboard');

            return;
        }

        if ($toast = session()->pull('toast')) {
            $this->dispatch('toast', ...$toast);
        }
    }

    public function resend(ResendVerificationAction $resend_verification_action): void
    {
        $this->resend_error = null;

        $result = $resend_verification_action->execute($this->user, request());

        if ($result === ResendVerificationResult::RateLimited) {
            $this->resend_error = 'rate_limited';

            return;
        }

        if ($result === ResendVerificationResult::AlreadyVerified) {
            $this->redirectRoute('dashboard');

            return;
        }

        $this->dispatch('toast',
            title: __('Verification email sent'),
            variant: 'success',
            duration: 3000,
            position: 'bottom-center',
        );
    }
};
