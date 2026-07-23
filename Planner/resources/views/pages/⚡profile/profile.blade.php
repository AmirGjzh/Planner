<div class="flex-1 px-4 sm:px-8 md:px-16 pb-6 pt-4 flex flex-col">
    <div class="mb-6 ml-2">
        <h1 class="font-bold text-md mine-text-primary">My Profile</h1>
    </div>
    <div class="flex flex-col md:flex-row gap-6">
        <div class="mine-card flex-1 p-4 sm:p-6  flex flex-col sm:flex-row sm:items-center sm:justify-between md:flex-col md:items-center md:justify-center gap-4">
            <div class="flex items-center gap-4 md:flex-col">
                <div class="md:mt-20 size-20 rounded-full mine-badge-primary flex justify-center items-center">
                    <h1 class="font-bold text-xl">{{ strtoupper($this->user->firstname && $this->user->lastname ? substr($this->user->firstname, 0, 1) . substr($this->user->lastname, 0, 1) : substr($this->user->username, 0, 2)) }}</h1>
                </div>
                <div class="md:text-center md:flex-0 sm:flex-1 md:my-2">
                    <h1 class="font-bold text-md mine-text-primary mb-1">{{ $this->user->username }}</h1>
                    <p class="font-medium text-sm mine-text-secondary">{{ $this->user->email }}</p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 md:w-full md:px-4 md:mt-auto">
                <x-mine.modal.trigger class="w-full" id="edit-profile-form">
                    <x-mine.button
                        type="button"
                        class=" mine-btn-outline-primary">
                        <div class="flex justify-center items-center gap-2">
                            <x-mine.icon name="pencil-square" class="inline"/>
                            Edit Profile
                        </div>
                    </x-mine.button>
                </x-mine.modal.trigger>
            </div>
        </div>

        <div class="md:flex-2 flex flex-col">
            <div class="mine-card p-6 sm:p-8 flex flex-col gap-4">
                <div class="mb-4">
                    <h1 class="font-bold text-sm mine-text-primary">Personal Information</h1>
                </div>
                <div class="flex justify-between px-2">
                    <p class="text-sm font-medium mine-text-secondary">Firstname</p>
                    <p class="text-sm font-medium mine-text-secondary">
                        {{ $this->user->firstname ?? '—' }}
                    </p>
                </div>
                <div class="flex justify-between px-2">
                    <p class="text-sm font-medium mine-text-secondary">Lastname</p>
                    <p class="text-sm font-medium mine-text-secondary">
                        {{ $this->user->lastname ?? '—' }}
                    </p>
                </div>
                <div class="flex justify-between px-2">
                    <p class="text-sm font-medium mine-text-secondary">Birthday</p>
                    <p class="text-sm font-medium mine-text-secondary">
                        {{ $this->user->birthday?->format('Y-m-d') ?? '—' }}
                    </p>
                </div>
                <div class="flex justify-between px-2">
                    <p class="text-sm font-medium mine-text-secondary">Gender</p>
                    <p class="text-sm font-medium mine-text-secondary">{{ $this->user->gender ? ucfirst($this->user->gender->value) : '—' }}
                    </p>
                </div>
                <div class="flex justify-between px-2">
                    <p class="text-sm font-medium mine-text-secondary">Country</p>
                    <p class="text-sm font-medium mine-text-secondary">{{ $this->countries[$this->user->country] ?? '—' }}
                    </p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 px-4 sm:px-6 py-4 mt-6 sm:justify-between alert-danger-box">
                <div class="flex items-center justify-center gap-4">
                    <div class="size-12 rounded-full mine-badge-danger flex justify-center items-center">
                        <x-mine.icon name="trash" class="size-6"/>
                    </div>
                    <p class="text-sm font-medium mine-text-secondary">Delete your account and all of your
                        data.</p>
                </div>
                <div class="flex justify-center items-center">
                    <x-mine.modal.trigger class="w-full" id="delete-account-confirmation">
                        <x-mine.button
                            type="button"
                            class="mine-btn-outline-danger"
                        >
                            Delete Account
                        </x-mine.button>
                    </x-mine.modal.trigger>
                </div>
            </div>
        </div>
    </div>


    <x-mine.modal id="edit-profile-form" :close-by-clicking-away="false" :close-by-escaping="false" width="xl">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Edit your information</h2>
                <button @click="close(); $wire.cancelEdit()"
                        class="mine-text-primary hover:bg-(--mine-btn-x-bg-hover) p-2 rounded-xl hover:cursor-pointer">
                    <x-mine.icon name="x-mark"/>
                </button>
            </div>

            @if($edit_success === 'updated')
                <div class="mb-4">
                    <x-mine.alert variant="success" title="Profile updated.">Your profile has been updated
                        successfully.
                    </x-mine.alert>
                </div>
            @endif

            @if($edit_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="Too many attempts.">Please try again in a minute.
                    </x-mine.alert>
                </div>
            @endif

            @if($edit_error === 'username_taken')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="Username already taken.">That username is already in use.
                    </x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="editProfile">
                <div>
                    <x-mine.input label="Username" wire:model="username"/>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <x-mine.input label="Firstname" wire:model="firstname" placeholder="Your Firstname" />
                    <x-mine.input label="Lastname" wire:model="lastname" placeholder="Your lastname" />
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <x-mine.select wire:model="gender" label="Gender" placeholder="Select gender" >
                        <x-mine.select.option value="male">Male</x-mine.select.option>
                        <x-mine.select.option value="female">Female</x-mine.select.option>
                    </x-mine.select>

                    <x-mine.select wire:model="country" label="Country" placeholder="Select Country"
                                 searchable>
                        @foreach($this->countries as $code => $name)
                            <x-mine.select.option wire:key="{{ $code }}"
                                                  value="{{ $code }}">{{ $name }}</x-mine.select.option>
                        @endforeach
                    </x-mine.select>
                </div>
                <div>
                    <x-mine.datepicker mode="single" selectable-months selectable-years position="top-end"
                                       :years-range="[-100, 100]" wire:model="birthday" label="Birthday"/>
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelEdit()" class="mine-btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="editProfile" class="mine-btn-primary">Save</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="delete-account-confirmation" :close-by-clicking-away="false" :close-by-escaping="false"
                  width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Delete your account</h2>
                <button @click="close(); $wire.cancelDelete()"
                        class="mine-text-primary hover:bg-(--mine-btn-x-bg-hover) p-2 rounded-xl hover:cursor-pointer">
                    <x-mine.icon name="x-mark"/>
                </button>
            </div>

            @if($delete_error === 'rate_limited')
                <x-mine.alert variant="warning" title="Too many attempts." class="mb-4">Please try again in a minute.
                </x-mine.alert>
            @endif

            @if($delete_error === 'wrong_password')
                <x-mine.alert variant="danger" title="Wrong password." class="mb-4">The password you entered is
                    incorrect.
                </x-mine.alert>
            @endif

            @unless($delete_error)
                <x-mine.alert variant="warning" title="Be Careful!" class="mb-4">
                    This action is permanent and cannot be undone.<br>Enter your password to continue.
                </x-mine.alert>
            @endunless

            <form wire:submit="deleteAccount" class="flex flex-col gap-6"
                @open-modal.window="if ($event.detail.id === 'delete-account-confirmation') { $nextTick(() => $el.querySelector('input')?.focus()) }">
                <x-mine.input wire:model="password" label="Password"
                              placeholder="Enter your password" type="password" leftIcon="lock-closed" />
                <div class="flex gap-4 justify-between items-center">
                    <x-mine.button type="button" @click="close(); $wire.cancelDelete()"
                                   class="mine-btn-ghost">Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="deleteAccount" class="mine-btn-danger">Delete Account
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>
</div>
