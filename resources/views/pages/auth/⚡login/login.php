<?php

use App\Actions\Auth\LoginUserAction;
use App\Enums\LoginResult;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth')] class extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public ?string $login_error = null;

    public function login(LoginUserAction $login_user_action)
    {
        $this->login_error = null;

        $credentials = $this->validate();
        $result = $login_user_action->execute(
            $credentials['email'],
            $credentials['password'],
            $this->remember,
            request()
        );

        $this->login_error = match ($result) {
            LoginResult::RateLimited => 'rate_limited',
            LoginResult::Fail => 'invalid',
            LoginResult::Success => null,
        };

        if ($result === LoginResult::Success) {
            return $this->redirectRoute('dashboard', navigate: true);
        }

        $this->reset(['password']);
    }

    protected function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => 'Email is required.',
            'email.email' => 'Email is not a valid email address.',
            'password.required' => 'Password is required.',
        ];
    }
};
