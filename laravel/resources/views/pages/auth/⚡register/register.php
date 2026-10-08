<?php

use App\Actions\Auth\RegisterUserAction;
use App\Enums\RegisterResult;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth', ['title' => 'Create your account'])] class extends Component
{
    public string $username = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?string $register_error = null;

    public function register(RegisterUserAction $register_user_action): void
    {
        $this->register_error = null;

        $this->username = trim($this->username);
        $this->email = trim($this->email);

        try {
            $credentials = $this->validate();
        } catch (ValidationException $exception) {
            $this->dispatch('focus-first-error');

            throw $exception;
        }

        $result = $register_user_action->execute(
            $credentials['username'],
            $credentials['email'],
            $credentials['password'],
            request()
        );

        if ($result === RegisterResult::Success) {
            session()->flash('toast', [
                'title' => __('Your account is ready, sign in to continue'),
                'variant' => 'success',
                'duration' => 6000,
                'position' => 'bottom-center',
            ]);

            $this->redirectRoute('login', navigate: true);

            return;
        }

        $this->register_error = match ($result) {
            RegisterResult::RateLimited => 'rate_limited',
            RegisterResult::UsernameTaken => 'username_taken',
            RegisterResult::EmailTaken => 'email_taken',
        };

        $this->reset('password', 'password_confirmation');
    }

    protected function rules(): array
    {
        return [
            'username' => ['required', 'regex:/^[a-zA-Z][a-zA-Z0-9_-]{2,29}$/'],
            // 255 is the email column's width: email:rfc alone accepts longer
            // strings and the INSERT would fail with SQLSTATE 22001.
            'email' => ['required', 'email:rfc', 'max:255'],
            'password' => ['required', 'confirmed', 'min:8', 'max:256'],
            'password_confirmation' => ['required'],
        ];
    }

    protected function messages(): array
    {
        return [
            'username.required' => __('Username is required.'),
            'username.regex' => __('Start with a letter, use 3 to 30 characters.'),
            'email.required' => __('Email is required.'),
            'email.email' => __('Enter a valid email.'),
            'email.max' => __('Use at most 255 characters.'),
            'password.required' => __('Password is required.'),
            'password.confirmed' => __('The passwords don\'t match.'),
            'password.min' => __('Use at least 8 characters.'),
            'password.max' => __('Use at most 256 characters.'),
            'password_confirmation.required' => __('Confirm your password.'),
        ];
    }
};
