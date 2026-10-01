<?php

use App\Actions\Auth\ResetPasswordAction;
use App\Enums\ResetPasswordResult;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Layout('layouts::auth', ['title' => 'Choose a new password'])] class extends Component
{
    #[Url]
    public string $token = '';

    #[Url]
    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?string $reset_error = null;

    public function mount(): void
    {
        if ($this->token === '' || ! filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->reset_error = 'invalid_link';
        }
    }

    public function resetPassword(ResetPasswordAction $reset_password_action)
    {
        $this->reset_error = null;

        if ($this->token === '') {
            $this->reset_error = 'invalid_link';

            return;
        }

        if (! filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->reset_error = 'invalid_link';

            return;
        }

        try {
            $credentials = $this->validate();
        } catch (ValidationException $exception) {
            $this->dispatch('focus-first-error');

            throw $exception;
        }

        $result = $reset_password_action->execute(
            $this->email,
            $this->token,
            $credentials['password'],
            request()
        );

        $this->reset_error = match ($result) {
            ResetPasswordResult::RateLimited => 'rate_limited',
            ResetPasswordResult::InvalidLink => 'invalid_link',
            ResetPasswordResult::SameAsCurrent => 'same_password',
            ResetPasswordResult::Reset => null,
        };

        if ($result === ResetPasswordResult::Reset) {
            session()->flash('toast', [
                'title' => __('Your password has been reset, sign in to continue'),
                'variant' => 'success',
                'duration' => 6000,
                'position' => 'bottom-center',
            ]);

            return $this->redirectRoute('login', navigate: true);
        }

        $this->reset(['password', 'password_confirmation']);
    }

    protected function rules(): array
    {
        return [
            'password' => ['required', 'confirmed', 'min:8', 'max:256'],
            'password_confirmation' => ['required'],
        ];
    }

    protected function messages(): array
    {
        return [
            'password.required' => __('Password is required.'),
            'password.confirmed' => __('The passwords don\'t match.'),
            'password.min' => __('Use at least 8 characters.'),
            'password.max' => __('Use at most 256 characters.'),
            'password_confirmation.required' => __('Confirm your password.'),
        ];
    }
};
