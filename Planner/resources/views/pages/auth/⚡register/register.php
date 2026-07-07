<?php

use App\Actions\Auth\RegisterUserAction;
use App\Enums\RegisterResult;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth')] class extends Component
{
    public string $username = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?string $register_error = null;

    public function register(RegisterUserAction $register_user_action)
    {
        $credentials = $this->validate();
        $result = $register_user_action->execute(
            $credentials['username'],
            $credentials['email'],
            $credentials['password'],
            request()
        );

        $this->register_error = match ($result) {
            RegisterResult::Success => null,
            RegisterResult::RateLimited => 'rate_limited',
            RegisterResult::UsernameTaken => 'username_taken',
            RegisterResult::EmailTaken => 'email_taken',
        };

        if ($result === RegisterResult::Success) {
            return $this->redirectRoute('login', navigate: true);
        }

        $this->reset('password', 'password_confirmation');
    }

    protected function rules(): array
    {
        return [
            'username' => ['required', 'regex:/^[a-zA-Z][a-zA-Z0-9_-]{2,29}$/'],
            'email' => ['required', 'email:rfc'],
            'password' => ['required', 'confirmed', 'min:8'],
            'password_confirmation' => ['required'],
        ];
    }

    protected function messages(): array
    {
        return [
            'username.required' => 'The username is required.',
            'username.regex' => 'The username must start with a letter and be 3–30 characters.',
            'email.required' => 'The email is required.',
            'email.email' => 'The email is not a valid email address.',
            'password.required' => 'The password is required.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 8 characters.',
            'password_confirmation.required' => 'The password confirmation is required.',
        ];
    }
};
