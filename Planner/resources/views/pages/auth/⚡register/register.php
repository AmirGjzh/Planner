<?php

use App\Actions\Auth\RegisterUserAction;
use App\Enums\RegisterResult;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    #[Validate(['required'], onUpdate: false)]
    #[Validate('regex:/^[a-zA-Z][a-zA-Z0-9_-]{2,29}$/', message: 'Username must start with a letter and contain only letters, numbers, underscores and hyphens.', onUpdate: false)]
    public string $user_name = '';

    #[Validate(['required', 'email:rfc'], onUpdate: false)]
    public string $email = '';

    #[Validate(['required', 'confirmed', 'min:8'], onUpdate: false)]
    public string $password = '';

    #[Validate(['required'], onUpdate: false)]
    public string $password_confirmation = '';

    public function register(RegisterUserAction $registerUserAction)
    {
        $credentials = $this->validate();
        $result = $registerUserAction->execute(
            $credentials['user_name'],
            $credentials['email'],
            $credentials['password'],
            request()
        );
        if ($result === RegisterResult::Success) {
            return $this->redirectRoute('login', navigate: true);
        }
        if ($result === RegisterResult::RateLimited) {
            $this->addError('register', 'Too many sign up attempts. Please try again in a minute.');
            $this->reset('password', 'password_confirmation');

            return;
        }
        if ($result === RegisterResult::UsernameTaken) {
            $this->addError('user_name', 'Username already taken.');
            $this->reset('password', 'password_confirmation');

            return;
        }
        $this->addError('email', 'Email already taken.');
        $this->reset('password', 'password_confirmation');
    }
};
