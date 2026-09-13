<div class="flex-1 px-4 sm:px-8 md:px-16 pb-6 pt-4 flex flex-col">
    <x-mine.animate class="mb-6">
        <h1 class="font-bold text-base mine-text-primary">{{ __('My profile') }}</h1>
    </x-mine.animate>
    <div class="flex flex-col md:flex-row gap-6">
        <x-mine.animate delay="50" class="flex-1 mine-card p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between md:flex-col md:items-center md:justify-center gap-4">
            <div class="flex items-center gap-4 md:flex-col">
                <div class="md:mt-20 size-20 shrink-0 rounded-full mine-badge-primary flex justify-center items-center">
                    <h1 class="font-bold text-xl pt-1">
                        {{ $this->user->initials() }}
                    </h1>
                </div>
                <div class="md:text-center md:flex-0 sm:flex-1 md:my-2 min-w-0">
                    <h1 class="font-bold text-base mine-text-primary mb-1 truncate">{{ $this->user->username }}</h1>
                    <p class="font-medium text-sm mine-text-secondary truncate">{{ $this->user->email }}</p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 md:w-full md:px-4 md:mt-auto">
                <x-mine.modal.trigger class="w-full" id="edit-profile-form">
                    <x-mine.button type="button" class=" mine-btn-primary">
                        <p class="text-sm">
                            {{ __('Edit profile') }}
                        </p>
                    </x-mine.button>
                </x-mine.modal.trigger>
            </div>
        </x-mine.animate>

        <div class="md:flex-2 flex flex-col">
            <x-mine.animate delay="100" class="mine-card px-4 py-6 sm:p-8 flex flex-col gap-4">
                <div class="mb-2">
                    <h1 class="font-bold text-sm mine-text-primary px-1">{{ __('Personal information') }}</h1>
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
                        <p class="text-sm font-medium mine-text-secondary">{{ $label }}</p>
                        <p class="text-sm font-medium mine-text-secondary max-w-40 min-w-0 truncate">
                            {{ $value ?? '—' }}
                        </p>
                    </div>
                @endforeach
                <div class="mb-2 mt-4">
                    <h1 class="font-bold text-sm mine-text-primary px-1">{{ __('Preferences') }}</h1>
                </div>
                <div class="flex justify-between px-2">
                    <p class="text-sm font-medium mine-text-secondary">{{ __('Theme') }}</p>
                    <p class="text-sm font-medium mine-text-secondary max-w-40 min-w-0 truncate">{{ __(ucfirst($this->user->theme)) }}</p>
                </div>
                <div class="flex justify-between px-2">
                    <p class="text-sm font-medium mine-text-secondary">{{ __('Language') }}</p>
                    <p class="text-sm font-medium mine-text-secondary max-w-40 min-w-0 truncate">{{ $this->user->locale == 'fa' ? 'فارسی' : "English" }}</p>
                </div>
            </x-mine.animate>
            <x-mine.animate delay="150" class="flex flex-col sm:flex-row gap-4 px-4 sm:px-6 py-4 mt-6 sm:justify-between mine-alert-danger-box">
                <div class="flex items-center justify-center gap-4">
                    <div class="size-12 rounded-full mine-badge-danger flex justify-center items-center">
                        <x-mine.icon name="Trash5" size="24" weight="filled" />
                    </div>
                    <p class="text-sm font-medium mine-text-secondary pt-1">{{ __('Delete your account and all your data.') }}</p>
                </div>
                <div class="flex justify-center items-center">
                    <x-mine.modal.trigger class="w-full" id="delete-account-confirmation">
                        <x-mine.button type="button" class="mine-btn-danger">
                            <p class="text-sm">
                                {{ __('Delete account') }}
                            </p>
                        </x-mine.button>
                    </x-mine.modal.trigger>
                </div>
            </x-mine.animate>
        </div>
    </div>

    <x-mine.modal id="edit-profile-form" :close-by-clicking-away="false" :close-by-escaping="false" width="xl">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base font-semibold mine-text-primary">{{ __('Edit your information') }}</h2>
                <button type="button" @click="close(); $wire.cancelEdit()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="Xmark" weight="filled" size="16" />
                </button>
            </div>

            @if($edit_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="{{ __('Too many edit-profile attempts!') }}">{{ __('Try again in a minute.') }}
                    </x-mine.alert>
                </div>
            @endif

            @if($edit_error === 'username_taken')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="{{ __('Username taken!') }}">{{ __('Try a different username.') }}
                    </x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="editProfile">
                <div>
                    <x-mine.input label="{{ __('Username') }}" wire:model="username" placeholder="{{ __('Your username') }}" leftIcon="User4" />
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <x-mine.input label="{{ __('Firstname') }}" wire:model="firstname" placeholder="{{ __('Your firstname') }}" leftIcon="User4" />
                    <x-mine.input label="{{ __('Lastname') }}" wire:model="lastname" placeholder="{{  __('Your lastname')  }}" leftIcon="User4" />
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <x-mine.select wire:model="locale" label="{{ __('Language') }}" placeholder="{{ __('Select your language') }}" leftIcon="Language">
                        <x-mine.select.option value="fa">فارسی</x-mine.select.option>
                        <x-mine.select.option value="en">English</x-mine.select.option>
                    </x-mine.select>

                    <x-mine.select wire:model="theme" label="{{ __('Theme') }}" placeholder="{{ __('Select your theme') }}" leftIcon="Sun">
                        @foreach(config('themes.name') as $name)
                            <x-mine.select.option wire:key="{{ $name }}"
                                value="{{ $name }}">{{ __(ucfirst($name)) }}</x-mine.select.option>
                        @endforeach
                    </x-mine.select>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <x-mine.select wire:model="gender" label="{{ __('Gender') }}" placeholder="{{ __('Select your gender') }}" leftIcon="Male">
                        <x-mine.select.option value="male">{{ __('Male') }}</x-mine.select.option>
                        <x-mine.select.option value="female">{{ __('Female') }}</x-mine.select.option>
                    </x-mine.select>

                    <x-mine.select wire:model="country" label="{{ __('Country') }}" placeholder="{{ __('Select your country') }}" searchable leftIcon="Earth">
                        @foreach($this->countries as $code => $name)
                            <x-mine.select.option wire:key="{{ $code }}"
                                value="{{ $code }}">{{ $name }}</x-mine.select.option>
                        @endforeach
                    </x-mine.select>
                </div>
                <div>
                    <x-mine.datepicker mode="single" selectable-months selectable-years position="top-{{ app()->isLocale('en') ? 'end' : 'start' }}"
                        :years-range="[-100, 0]" wire:model="birthday" label="{{ __('Birthday') }}" leftIcon="Calendar" />
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelEdit()" class="mine-btn-ghost">
                        <p class="text-sm">
                            {{ __('Cancel') }}
                        </p>
                    </x-mine.button>
                    <x-mine.button wire:target="editProfile" class="mine-btn-primary">
                        <p class="text-sm">
                            {{ __('Save') }}
                        </p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="delete-account-confirmation" :close-by-clicking-away="false" :close-by-escaping="false"
        width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base font-semibold mine-text-primary">{{ __('Delete your account') }}</h2>
                <button type="button" @click="close(); $wire.cancelDelete()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="Xmark" weight="filled" size="16" />
                </button>
            </div>

            @if($delete_error === 'rate_limited')
                <x-mine.alert variant="warning" title="{{ __('Too many delete-account attempts!') }}" class="mb-4">{{ __('Try again in a minute.') }}
                </x-mine.alert>
            @endif

            @if($delete_error === 'wrong_password')
                <x-mine.alert variant="danger" title="{{ __('Wrong password!') }}" class="mb-4">{{ __('The password you entered is incorrect.') }}
                </x-mine.alert>
            @endif

            @unless($delete_error)
                <x-mine.alert variant="warning" title="{{ __('Be careful!') }}" class="mb-4">
                    {{ __('This action is permanent, enter your password to continue.') }}
                </x-mine.alert>
            @endunless

            <form wire:submit="deleteAccount" class="flex flex-col gap-6">
                <x-mine.input wire:model="password" label="{{ __('Password') }}" placeholder="{{ __('Your password') }}" type="password"
                    leftIcon="Lock" />
                <div class="flex gap-4 justify-between items-center">
                    <x-mine.button type="button" @click="close(); $wire.cancelDelete()" class="mine-btn-ghost">
                        <p class="text-sm">
                            {{ __('Cancel') }}
                        </p>
                    </x-mine.button>
                    <x-mine.button wire:target="deleteAccount" wire:loading:disabled class="mine-btn-danger">
                        <p class="text-sm">
                            {{ __('Delete account') }}
                        </p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>
</div>