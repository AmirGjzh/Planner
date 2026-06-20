<?php

use Livewire\Component;

new class extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login()
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ]);

        if (Auth::attempt(
            [
                'email' => $credentials['email'],
                'password' => $credentials['password'],
            ],
            $this->remember
        )) {
            request()->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        $this->addError('email', 'ایمیل یا رمز عبور اشتباه است.');
    }
};
