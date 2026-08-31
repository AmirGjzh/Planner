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

    public ?string $delete_error = null;

    public function mount(): void
    {
        $this->fillForm();
    }

    #[Computed]
    public function countries(): array
    {
        return cache()->rememberForever('countries-list-'.app()->getLocale(), function (): array {
            $countries = Countries::getNames(app()->getLocale());
            if (app()->isLocale('fa')) {
                (new Collator('fa_IR'))->asort($countries);
            } else {
                asort($countries);
            }

            return $countries;
        });
    }

    public function editProfile(UpdateProfileAction $action): void
    {
        $this->edit_error = null;

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
            return;
        }
        $this->fillForm();
        $this->resetValidation();
        $this->dispatch('close-modal', id: 'edit-profile-form');
        $this->dispatch('profile-updated',
            username: $this->username,
            email: $this->user->email,
            firstname: $this->firstname ?? '',
            lastname: $this->lastname ?? '',
            initials: $this->user->initials(),
        );
        $this->dispatch('toast',
            title: __('Profile updated'),
            variant: 'info',
            duration: 3000,
            position: 'bottom-center'
        );
    }

    public function cancelEdit(): void
    {
        $this->edit_error = null;
        $this->fillForm();
        $this->resetValidation();
    }

    public function deleteAccount(DeleteAccountAction $deleteAccountAction): void
    {
        $this->delete_error = null;

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
        session()->flash('toast', [
            'title' => __('Account deleted'),
            'variant' => 'success',
            'duration' => 6000,
            'position' => 'bottom-center',
        ]);
        $this->redirectRoute('home', navigate: true);
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
            'username.required' => __('Username is required'),
            'username.regex' => __('Start with a letter, use 3 to 30 characters'),
            'firstname.min' => __('Firstname must be at least 2 characters'),
            'firstname.max' => __('Firstname cannot exceed 50 characters'),
            'firstname.regex' => __('Firstname can only contain letters, spaces, hyphens and apostrophes'),
            'lastname.min' => __('Lastname must be at least 2 characters'),
            'lastname.max' => __('Lastname cannot exceed 50 characters'),
            'lastname.regex' => __('Lastname can only contain letters, spaces, hyphens and apostrophes'),
            'gender.enum' => __('Selected gender is invalid'),
            'country.in' => __('Selected country is invalid'),
            'birthday.date' => __('Birthdate must be a valid date'),
            'birthday.before_or_equal' => __('Birthdate must be today or earlier'),
            'password.required' => __('Password is required'),
        ];
    }
};
