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

    public function mount()
    {
        if ($toast = session()->pull('toast')) {
            $this->dispatch('toast', ...$toast);
        }
    }

    public function register(RegisterUserAction $register_user_action)
    {
        $this->register_error = null;

        $credentials = $this->validate();
        $result = $register_user_action->execute(
            $credentials['username'],
            $credentials['email'],
            $credentials['password'],
            request()
        );

        if ($result === RegisterResult::Success) {
            session()->flash('toast', [
                'title' => __('Account created successfully, Please sign in'),
                'variant' => 'success',
                'duration' => 6000,
                'position' => 'bottom-center',
            ]);
            return $this->redirectRoute('login', navigate: true);
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
            'email' => ['required', 'email:rfc'],
            'password' => ['required', 'confirmed', 'min:8'],
            'password_confirmation' => ['required'],
        ];
    }

    protected function messages(): array
    {
        return [
            'username.required' => __('Username is required.'),
            'username.regex' => __('Username must start with a letter and be 3–30 characters.'),
            'email.required' => __('Email address is required.'),
            'email.email' => __('Please enter a valid email address.'),
            'password.required' => __('Password is required.'),
            'password.confirmed' => __('Password confirmation does not match.'),
            'password.min' => __('Password must be at least 8 characters.'),
            'password_confirmation.required' => __('Confirm password is required.'),
        ];
    }
};
