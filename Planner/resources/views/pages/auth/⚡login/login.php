<?php

use App\Actions\Auth\LoginUserAction;
use App\Enums\LoginResult;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts::auth')] class extends Component
{
    #[Validate(['required', 'email'], onUpdate: false)]
    public string $email = '';

    #[Validate(['required'], onUpdate: false)]
    public string $password = '';

    #[Validate(['boolean'], onUpdate: false)]
    public bool $remember = true;

    public function login(LoginUserAction $loginUserAction)
    {
        $credentials = $this->validate();
        $result = $loginUserAction->execute(
            $credentials['email'],
            $credentials['password'],
            $this->remember,
            request()
        );
        if ($result === LoginResult::Success) {
            return $this->redirectRoute('dashboard', navigate: true);
        }
        if ($result === LoginResult::RateLimited) {
            $this->addError('login', 'Too many login attempts. Please try again in a minute.');

            return;
        }
        $this->reset(['password']);
        $this->addError('login', 'Wrong email or password.');
    }
};
