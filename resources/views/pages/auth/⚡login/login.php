<?php

use App\Actions\Auth\LoginUserAction;
use App\Enums\LoginResult;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth')] class extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = true;

    public ?string $login_error = null;

    public function mount()
    {
        if ($toast = session()->pull('toast')) {
            $this->dispatch('toast', ...$toast);
        }
    }

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
            session()->flash('toast', [
                'title' => __('Signed in successfully'),
                'variant' => 'success',
                'duration' => 6000,
                'position' => 'bottom-center',
            ]);

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
            'email.required' => __('Email address is required.'),
            'email.email' => __('Please enter a valid email address.'),
            'password.required' => __('Password is required.'),
        ];
    }
};
