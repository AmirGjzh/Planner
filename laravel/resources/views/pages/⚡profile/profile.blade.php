<div class="flex flex-1 flex-col px-4 pt-4 pb-6 sm:px-8 md:px-16">
    <x-mine.animate class="mb-6">
        <h1 class="mine-text-primary text-base font-bold">{{ __('My profile') }}</h1>
    </x-mine.animate>
    <div class="flex flex-col gap-6 md:flex-row">
        <x-mine.animate
            delay="50"
            class="mine-card flex flex-1 flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-6 md:flex-col md:items-center md:justify-center"
        >
            <div class="flex items-center gap-4 md:flex-col">
                <div class="mine-badge-primary flex size-20 shrink-0 items-center justify-center rounded-full md:mt-20">
                    <h1 class="pt-1 text-xl font-bold">{{ $this->user->initials() }}</h1>
                </div>
                <div class="min-w-0 sm:flex-1 md:my-2 md:flex-0 md:text-center">
                    <h1 class="mine-text-primary mb-1 truncate text-base font-bold">{{ $this->user->username }}</h1>
                    <p class="mine-text-secondary truncate text-sm font-medium">{{ $this->user->email }}</p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 md:mt-auto md:w-full md:px-4">
                <x-mine.modal.trigger class="w-full" id="edit-profile-form">
                    <x-mine.button type="button" class="mine-btn-primary">
                        <p class="text-sm">{{ __('Edit profile') }}</p>
                    </x-mine.button>
                </x-mine.modal.trigger>
            </div>
        </x-mine.animate>

        <div class="flex flex-col md:flex-2">
            <x-mine.animate delay="100" class="mine-card flex flex-col gap-4 px-4 py-6 sm:p-8">
                <div class="mb-2">
                    <h1 class="mine-text-primary px-1 text-sm font-bold">{{ __('Personal information') }}</h1>
                </div>
                @foreach ([
                    __('Firstname') => $this->user->firstname,
                    __('Lastname') => $this->user->lastname,
                    __('Birthday') => $this->user->birthday
                        ? (app()->isLocale('fa')
                            ? \App\Support\Jalali::format($this->user->birthday, 'yyyy/MM/dd')
                            : $this->user->birthday->format('Y-m-d'))
                        : null,
                    __('Gender') => $this->user->gender ? __(ucfirst($this->user->gender->value)) : null,
                    __('Country') => $this->countries[$this->user->country] ?? null,
                ] as $label => $value)
                    <div class="flex justify-between px-2">
                        <p class="mine-text-secondary text-sm font-medium">{{ $label }}</p>
                        <p class="mine-text-secondary max-w-40 min-w-0 truncate text-sm font-medium">
                            {{ $value ?? '—' }}
                        </p>
                    </div>
                @endforeach
                <div class="mt-4 mb-2">
                    <h1 class="mine-text-primary px-1 text-sm font-bold">{{ __('Preferences') }}</h1>
                </div>
                <div class="flex justify-between px-2">
                    <p class="mine-text-secondary text-sm font-medium">{{ __('Theme') }}</p>
                    <p class="mine-text-secondary max-w-40 min-w-0 truncate text-sm font-medium">
                        {{ __(ucfirst($this->user->theme)) }}
                    </p>
                </div>
                <div class="flex justify-between px-2">
                    <p class="mine-text-secondary text-sm font-medium">{{ __('Language') }}</p>
                    <p class="mine-text-secondary max-w-40 min-w-0 truncate text-sm font-medium">
                        {{ $this->user->locale == 'fa' ? 'فارسی' : "English" }}
                    </p>
                </div>
            </x-mine.animate>
            <x-mine.animate
                delay="150"
                class="mine-alert-danger-box mt-6 flex flex-col gap-4 px-4 py-4 sm:flex-row sm:justify-between sm:px-6"
            >
                <div class="flex items-center justify-center gap-4">
                    <div class="mine-badge-danger flex size-12 items-center justify-center rounded-full">
                        <x-mine.icon name="Trash5" size="24" weight="filled" />
                    </div>
                    <p class="mine-text-secondary pt-1 text-sm font-medium">
                        {{ __('Delete your account and all your data.') }}
                    </p>
                </div>
                <div class="flex items-center justify-center">
                    <x-mine.modal.trigger class="w-full" id="delete-account-confirmation">
                        <x-mine.button type="button" class="mine-btn-danger">
                            <p class="text-sm">{{ __('Delete account') }}</p>
                        </x-mine.button>
                    </x-mine.modal.trigger>
                </div>
            </x-mine.animate>
        </div>
    </div>

    <x-mine.modal id="edit-profile-form" :close-by-clicking-away="false" :close-by-escaping="false" width="xl">
        <div class="px-6 py-6 sm:px-8">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="mine-text-primary text-base font-semibold">{{ __('Edit your information') }}</h2>
                <button
                    type="button"
                    @click="
                        close();
                        $wire.cancelEdit();
                    "
                    class="mine-btn-icon rounded-xl p-2"
                >
                    <x-mine.icon name="Xmark" weight="filled" size="16" />
                </button>
            </div>

            @if ($edit_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="{{ __('Too many edit-profile attempts!') }}">
                        {{ __('Try again in a minute.') }}
                    </x-mine.alert>
                </div>
            @endif

            @if ($edit_error === 'username_taken')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="{{ __('Username taken!') }}">
                        {{ __('Try a different username.') }}
                    </x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="editProfile">
                <div>
                    <x-mine.input
                        label="{{ __('Username') }}"
                        wire:model="username"
                        placeholder="{{ __('Your username') }}"
                        leftIcon="User4"
                    />
                </div>
                <div class="flex flex-col gap-4 sm:flex-row">
                    <x-mine.input
                        label="{{ __('Firstname') }}"
                        wire:model="firstname"
                        placeholder="{{ __('Your firstname') }}"
                        leftIcon="User4"
                    />
                    <x-mine.input
                        label="{{ __('Lastname') }}"
                        wire:model="lastname"
                        placeholder="{{ __('Your lastname') }}"
                        leftIcon="User4"
                    />
                </div>
                <div class="flex flex-col gap-4 sm:flex-row">
                    <x-mine.select
                        wire:model="locale"
                        label="{{ __('Language') }}"
                        placeholder="{{ __('Select your language') }}"
                        leftIcon="Language"
                    >
                        <x-mine.select.option value="fa">فارسی</x-mine.select.option>
                        <x-mine.select.option value="en">English</x-mine.select.option>
                    </x-mine.select>

                    <x-mine.select
                        wire:model="theme"
                        label="{{ __('Theme') }}"
                        placeholder="{{ __('Select your theme') }}"
                        leftIcon="Sun"
                    >
                        @foreach (config('themes.name') as $name)
                            <x-mine.select.option
                                wire:key="{{ $name }}"
                                value="{{ $name }}"
                            >
                                {{ __(ucfirst($name)) }}</x-mine.select.option>
                        @endforeach
                    </x-mine.select>
                </div>
                <div class="flex flex-col gap-4 sm:flex-row">
                    <x-mine.select
                        wire:model="gender"
                        label="{{ __('Gender') }}"
                        placeholder="{{ __('Select your gender') }}"
                        leftIcon="Male"
                    >
                        <x-mine.select.option value="male">{{ __('Male') }}</x-mine.select.option>
                        <x-mine.select.option value="female">{{ __('Female') }}</x-mine.select.option>
                    </x-mine.select>

                    <x-mine.select
                        wire:model="country"
                        label="{{ __('Country') }}"
                        placeholder="{{ __('Select your country') }}"
                        searchable
                        leftIcon="Earth"
                    >
                        @foreach ($this->countries as $code => $name)
                            <x-mine.select.option
                                wire:key="{{ $code }}"
                                value="{{ $code }}"
                            >
                                {{ $name }}</x-mine.select.option>
                        @endforeach
                    </x-mine.select>
                </div>
                <div>
                    <x-mine.datepicker
                        mode="single"
                        selectable-months
                        selectable-years
                        position="top-{{ app()->isLocale('en') ? 'end' : 'start' }}"
                        :years-range="[-100, 0]"
                        wire:model="birthday"
                        label="{{ __('Birthday') }}"
                        leftIcon="Calendar"
                    />
                </div>
                <div class="mt-4 flex justify-end gap-4">
                    <x-mine.button
                        type="button"
                        @click="
                            close();
                            $wire.cancelEdit();
                        "
                        class="mine-btn-ghost"
                    >
                        <p class="text-sm">{{ __('Cancel') }}</p>
                    </x-mine.button>
                    <x-mine.button wire:target="editProfile" class="mine-btn-primary">
                        <p class="text-sm">{{ __('Save') }}</p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal
        id="delete-account-confirmation"
        :close-by-clicking-away="false"
        :close-by-escaping="false"
        width="lg"
    >
        <div class="px-6 py-6 sm:px-8">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="mine-text-primary text-base font-semibold">{{ __('Delete your account') }}</h2>
                <button
                    type="button"
                    @click="
                        close();
                        $wire.cancelDelete();
                    "
                    class="mine-btn-icon rounded-xl p-2"
                >
                    <x-mine.icon name="Xmark" weight="filled" size="16" />
                </button>
            </div>

            @if ($delete_error === 'rate_limited')
                <x-mine.alert variant="warning" title="{{ __('Too many delete-account attempts!') }}" class="mb-4">
                    {{ __('Try again in a minute.') }}
                </x-mine.alert>
            @endif

            @if ($delete_error === 'wrong_password')
                <x-mine.alert variant="danger" title="{{ __('Wrong password!') }}" class="mb-4">
                    {{ __('The password you entered is incorrect.') }}
                </x-mine.alert>
            @endif

            @unless ($delete_error)
                <x-mine.alert variant="warning" title="{{ __('Be careful!') }}" class="mb-4">
                    {{ __('This action is permanent, enter your password to continue.') }}
                </x-mine.alert>
            @endunless

            <form wire:submit="deleteAccount" class="flex flex-col gap-6">
                <x-mine.input
                    wire:model="password"
                    label="{{ __('Password') }}"
                    placeholder="{{ __('Your password') }}"
                    type="password"
                    leftIcon="Lock"
                />
                <div class="flex items-center justify-between gap-4">
                    <x-mine.button
                        type="button"
                        @click="
                            close();
                            $wire.cancelDelete();
                        "
                        class="mine-btn-ghost"
                    >
                        <p class="text-sm">{{ __('Cancel') }}</p>
                    </x-mine.button>
                    <x-mine.button wire:target="deleteAccount" wire:loading:disabled class="mine-btn-danger">
                        <p class="text-sm">{{ __('Delete account') }}</p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>
</div>
