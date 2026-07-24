<?php

use App\Actions\Auth\DeleteAccountAction;
use App\Actions\Profile\UpdateProfileAction;
use App\Enums\DeleteAccountResult;
use App\Enums\UpdateProfileResult;
use App\Enums\UserGender;
use App\Livewire\Concerns\HasUser;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Symfony\Component\Intl\Countries;

new class extends Component
{
    use HasUser;

    public string $username = '';

    public ?string $firstname = null;

    public ?string $lastname = null;

    public ?string $gender = null;

    public ?string $country = null;

    public ?string $birthday = null;

    public string $password = '';

    public ?string $edit_error = null;

    public ?string $edit_success = null;

    public ?string $delete_error = null;

    public function mount(): void
    {
        $this->fillForm();
    }

    #[Computed(cache: true, key: 'countries-list')]
    public function countries(): array
    {
        $countries = Countries::getNames('en');
        asort($countries);

        return $countries;
    }

    public function editProfile(UpdateProfileAction $action): void
    {
        $data = $this->validate();
        $result = $action->execute(
            $this->user,
            $data['username'],
            $data['firstname'],
            $data['lastname'],
            $data['gender'],
            $data['country'],
            $data['birthday'],
            request()
        );
        $this->edit_error = match ($result) {
            UpdateProfileResult::RateLimited => 'rate_limited',
            UpdateProfileResult::UsernameTaken => 'username_taken',
            UpdateProfileResult::Success => null,
        };
        if ($this->edit_error) {
            $this->edit_success = null;

            return;
        }
        $this->edit_success = 'updated';
        $this->fillForm();
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->edit_error = null;
        $this->edit_success = null;
        $this->fillForm();
        $this->resetValidation();
    }

    public function deleteAccount(DeleteAccountAction $deleteAccountAction): void
    {
        $data = $this->validate([
            'password' => ['required'],
        ]);
        $result = $deleteAccountAction->execute($this->user, $data['password'], request());
        $this->delete_error = match ($result) {
            DeleteAccountResult::RateLimited => 'rate_limited',
            DeleteAccountResult::WrongPassword => 'wrong_password',
            DeleteAccountResult::Success => null,
        };
        if ($this->delete_error) {
            return;
        }
        $this->reset('password');
        $this->redirectRoute('login', navigate: true);
    }

    public function cancelDelete(): void
    {
        $this->delete_error = null;
        $this->reset('password');
        $this->resetValidation();
    }

    private function fillForm(): void
    {
        $user = $this->user;
        $this->username = $user->username;
        $this->firstname = $user->firstname;
        $this->lastname = $user->lastname;
        $this->gender = $user->gender?->value;
        $this->country = $user->country;
        $this->birthday = $user->birthday?->format('Y-m-d');
    }

    protected function rules(): array
    {
        return [
            'username' => ['required', 'regex:/^[a-zA-Z][a-zA-Z0-9_-]{2,29}$/'],
            'firstname' => ['nullable', 'string', 'min:2', 'max:50', 'regex:/^[\p{L}\s\'-]+$/u'],
            'lastname' => ['nullable', 'string', 'min:2', 'max:50', 'regex:/^[\p{L}\s\'-]+$/u'],
            'gender' => ['nullable', Rule::enum(UserGender::class)],
            'country' => ['nullable', Rule::in(array_keys($this->countries()))],
            'birthday' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }

    protected function messages(): array
    {
        return [
            'username.required' => 'Username is required.',
            'username.regex' => 'Username must start with a letter and be 3–30 characters.',
            'firstname.min' => 'Firstname must be at least 2 characters.',
            'firstname.max' => 'Firstname may not be greater than 50 characters.',
            'firstname.regex' => 'Firstname may only contain letters, spaces, hyphens, and apostrophes.',
            'lastname.min' => 'Lastname must be at least 2 characters.',
            'lastname.max' => 'Lastname may not be greater than 50 characters.',
            'lastname.regex' => 'Lastname may only contain letters, spaces, hyphens, and apostrophes.',
            'gender.enum' => 'Selected gender is invalid.',
            'country.in' => 'Selected country is invalid.',
            'birthday.date' => 'Birthdate must be a valid date.',
            'birthday.before_or_equal' => 'Birthdate must be a date before or equal to today.',
            'password.required' => 'Password is required.',
        ];
    }
};
