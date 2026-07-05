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

    public ?string $loginError = null;

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
            // TODO: Maybe a welcome toast notification in dashboard page
            $this->loginError = null;
            return $this->redirectRoute('dashboard', navigate: true);
        }
        if ($result === LoginResult::RateLimited) {
            $this->loginError = 'limited';
            return;
        }
        $this->reset(['password']);
        $this->loginError = 'invalid';
    }

    protected function rules()
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    protected function messages()
    {
        return [
            'email.required' => 'The email is required.',
            'email.email' => 'The email is not a valid email address.',
            'password.required' => 'The password is required.',
        ];
    }
};
