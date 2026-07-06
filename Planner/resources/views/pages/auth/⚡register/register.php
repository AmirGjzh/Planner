<?php

use App\Actions\Auth\RegisterUserAction;
use App\Enums\RegisterResult;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts::auth')] class extends Component
{
    public string $user_name = '';

    public string $email = '';

    public string $password = '';

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

    protected function rules(): array
    {
        return [
            'user_name' => ['required', 'regex:/^[a-zA-Z][a-zA-Z0-9_-]{2,29}$/'],
            'email' => ['required', 'email:rfc'],
            'password' => ['required', 'confirmed', 'min:8'],
            'password_confirmation' => ['required'],
        ];
    }

    protected function messages(): array
    {
        return [

        ];
    }
};
