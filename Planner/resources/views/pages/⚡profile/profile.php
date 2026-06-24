<?php

use App\Actions\Profile\UpdateProfileAction;
use App\Enums\UpdateProfileResult;
use App\Enums\UserGender;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Symfony\Component\Intl\Countries;

new class extends Component
{
    #[Locked]
    public int $userId;

    public string $user_name = '';

    public ?string $first_name = null;

    public ?string $last_name = null;

    public ?string $gender = null;

    public ?string $country = null;

    public ?string $birth_date = null;

    public function mount(): void
    {
        $this->userId = auth()->id() ?? abort(403);
        $this->fillForm();
    }

    #[Computed]
    public function user(): User
    {
        return User::query()->findOrFail($this->userId);
    }

    public function editProfile(UpdateProfileAction $action): void
    {
        $data = $this->validate();
        $result = $action->execute($this->user, $data, request());
        if ($result === UpdateProfileResult::RateLimited) {
            $this->addError('profile', 'Too many profile update attempts. Please try again in a minute.');

            return;
        }
        if ($result === UpdateProfileResult::UsernameTaken) {
            $this->addError('user_name', 'Username already taken.');

            return;
        }
        unset($this->user);
        $this->fillForm();
        $this->resetValidation();
        $this->dispatch('close-modal', id: 'edit-profile-form');
    }

    public function cancelEdit(): void
    {
        $this->fillForm();
        $this->resetValidation();
        $this->dispatch('close-modal', id: 'edit-profile-form');
    }

    private function fillForm(): void
    {
        $user = $this->user;
        $this->user_name = $user->user_name;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->gender = $user->gender?->value;
        $this->country = $user->country;
        $this->birth_date = $user->birth_date?->format('Y-m-d');
    }

    #[Computed]
    public function countries(): array
    {
        $countries = Countries::getNames('en');
        asort($countries);

        return $countries;
    }

    protected function rules(): array
    {
        return [
            'user_name' => [
                'required',
                'regex:/^[a-zA-Z][a-zA-Z0-9_-]{2,29}$/',
            ],
            'first_name' => ['nullable', 'string', 'max:50'],
            'last_name' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', Rule::enum(UserGender::class)],
            'country' => ['nullable', Rule::in(array_keys(Countries::getNames('en')))],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }

    protected function messages(): array
    {
        return [
            'user_name.regex' => 'Username must start with a letter and contain only letters, numbers, underscores and hyphens.',
        ];
    }
};
