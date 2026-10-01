<?php

use App\Actions\Auth\ForgotPasswordAction;
use App\Enums\SendResetLinkResult;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth', ['title' => 'Reset your password'])] class extends Component
{
    public string $email = '';

    public bool $sent = false;

    public ?string $send_error = null;

    #[Computed]
    public function expireMinutes(): int
    {
        return (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire');
    }

    public function send(ForgotPasswordAction $forgot_password_action)
    {
        $this->send_error = null;

        $this->email = trim($this->email);

        try {
            $credentials = $this->validate();
        } catch (ValidationException $exception) {
            $this->dispatch('focus-first-error');

            throw $exception;
        }

        $result = $forgot_password_action->execute($credentials['email'], request());

        if ($result === SendResetLinkResult::RateLimited) {
            $this->send_error = 'rate_limited';

            return;
        }

        $this->sent = true;
    }

    public function restart(): void
    {
        $this->sent = false;
        $this->send_error = null;
        $this->reset(['email']);
        $this->resetValidation();
    }

    protected function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc'],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => __('Email is required.'),
            'email.email' => __('Enter a valid email.'),
        ];
    }
};
