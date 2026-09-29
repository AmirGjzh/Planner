<?php

use App\Actions\Auth\LoginUserAction;
use App\Enums\LoginResult;
use Illuminate\Support\Facades\Cookie;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth')] class extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public ?string $login_error = null;

    public function mount(): void
    {
        if ($toast = session()->pull('toast')) {
            $this->dispatch('toast', ...$toast);
        }

        if (request()->cookie(LoginUserAction::SESSION_HINT)) {
            $this->dispatch('toast',
                title: __('Your session has expired. Please sign in again.'),
                variant: 'info',
                duration: 6000,
                position: 'bottom-center'
            );
            Cookie::queue(Cookie::forget(LoginUserAction::SESSION_HINT));
        }
    }

    public function login(LoginUserAction $login_user_action)
    {
        $this->login_error = null;

        $this->email = trim($this->email);
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
            app()->setLocale(auth()->user()->locale);
            session()->flash('toast', [
                'title' => __('You\'re signed in'),
                'variant' => 'success',
                'duration' => 6000,
                'position' => 'bottom-center',
            ]);

            $intended = session()->pull('url.intended');

            if ($intended) {
                return $this->redirect($intended);
            }

            return $this->redirectRoute('dashboard');
        }

        $this->reset(['password']);
    }

    protected function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc'],
            'password' => ['required', 'max:256'],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => __('Email is required.'),
            'email.email' => __('Enter a valid email.'),
            'password.required' => __('Password is required.'),
            'password.max' => __('Use at most 256 characters.'),
        ];
    }
};
