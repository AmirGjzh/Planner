<div class="min-h-full bg-gradient-to-r from-slate-300 to-slate-50">
    <div class="rounded-lg shadow-lg m-2 p-5 sm:p-6 md:p-8 bg-gradient-to-r from-white to-slate-100">
        <div class="">
            <x-ui.heading level="h2" size="md">My Profile</x-ui.heading>
        </div>
        <x-ui.separator class="my-3" />
        <div>
            <x-ui.heading level="h4" size="sm" class="mb-4">Account Information</x-ui.heading>
            <div class="flex justify-between items-center m-1">
                <x-ui.text>Username</x-ui.text>
                <x-ui.text class="opacity-70">{{ $this->user->user_name }}</x-ui.text>
            </div>
            <div class="flex justify-between items-center m-1">
                <x-ui.text>Email</x-ui.text>
                <x-ui.text class="opacity-70">{{ $this->user->email }}</x-ui.text>
            </div>
        </div>
        <x-ui.separator class="my-3" />
        <div>
            <x-ui.heading level="h4" size="sm" class="mb-4">Personal Information</x-ui.heading>
            <div class="flex justify-between items-center m-1">
                <x-ui.text>Firstname</x-ui.text>
                <x-ui.text class="opacity-70">{{ $this->user->first_name ?? '—' }}</x-ui.text>
            </div>
            <div class="flex justify-between items-center m-1">
                <x-ui.text>Lastname</x-ui.text>
                <x-ui.text class="opacity-70">{{ $this->user->last_name ?? '—' }}</x-ui.text>
            </div>
            <div class="flex justify-between items-center m-1">
                <x-ui.text>Birthdate</x-ui.text>
                <x-ui.text class="opacity-70">{{ $this->user->birth_date?->format('Y/m/d') ?? '—' }}</x-ui.text>
            </div>
            <div class="flex justify-between items-center m-1">
                <x-ui.text>Gender</x-ui.text>
                <x-ui.text
                    class="opacity-70">{{ $this->user->gender ? ucfirst($this->user->gender->value) : '—' }}</x-ui.text>
            </div>
            <div class="flex justify-between items-center m-1">
                <x-ui.text>Country</x-ui.text>
                <x-ui.text
                    class="opacity-70">{{ $this->user->country ? ($this->countries[$this->user->country] ?? $this->user->country) : '—' }}</x-ui.text>
            </div>
            <x-ui.modal.trigger id="edit-profile-form">
                <x-ui.button class="mt-4 w-full rounded-lg bg-gradient-to-r from-slate-800 to-slate-600">Edit
                    Profile</x-ui.button>
            </x-ui.modal.trigger>
        </div>
    </div>
    <x-ui.modal bare backdrop="dark" position="center" width="3xl" id="edit-profile-form"
        heading="Edit your information" :close-by-clicking-away="false">
        <div class="flex flex-col m-5">
            <div class="flex justify-between items-center px-1">
                <x-ui.heading level="h2" size="md">Edit your information</x-ui.heading>
                <x-ui.icon wire:click="cancelEdit" name="x-mark"
                    class="size-7 opacity-80 hover:cursor-pointer"></x-ui.icon>
            </div>
            <x-ui.separator class="mt-4 mb-8" />
            <form class="p-2" wire:submit="editProfile">
                <div class="flex justify-between items-center gap-5 mb-4">
                    <x-ui.field>
                        <x-ui.label>Username</x-ui.label>
                        <x-ui.input wire:model="user_name" type="text" placeholder="" leftIcon="" />
                        <x-ui.error name="user_name" />
                    </x-ui.field>
                </div>
                <div class="flex justify-between items-center gap-5 mb-4">
                    <x-ui.field>
                        <x-ui.label>Firstname</x-ui.label>
                        <x-ui.input wire:model="first_name" type="text" placeholder="" leftIcon="" />
                        <x-ui.error name="first_name" />
                    </x-ui.field>
                    <x-ui.field>
                        <x-ui.label>Lastname</x-ui.label>
                        <x-ui.input wire:model="last_name" type="text" placeholder="" leftIcon="" />
                        <x-ui.error name="last_name" />
                    </x-ui.field>
                </div>
                <div class="flex justify-between items-center gap-5 mb-4">
                    <x-ui.field>
                        <x-ui.label>Gender</x-ui.label>
                        <x-ui.select placeholder="Select gender" wire:model="gender">
                            <x-ui.select.option value="male">Male</x-ui.select.option>
                            <x-ui.select.option value="female">Female</x-ui.select.option>
                        </x-ui.select>
                        <x-ui.error name="gender" />
                    </x-ui.field>
                    <x-ui.field>
                        <x-ui.label>Country</x-ui.label>
                        <x-ui.select {{-- icon="map-pin" --}} placeholder="Select country" wire:model="country"
                            searchable>
                            @foreach($this->countries as $code => $name)
                                <x-ui.select.option value="{{ $code }}">
                                    {{ $name }}
                                </x-ui.select.option>
                            @endforeach
                        </x-ui.select>
                        <x-ui.error name="country" />
                    </x-ui.field>
                </div>
                <div class="flex flex-col justify-between items-center gap-5 mb-12">
                    <x-ui.field>
                        <x-ui.label>Birthdate</x-ui.label>
                        <x-ui.date-picker selectable-months selectable-years :yearsRange="[-100, 100]" class="w-full"
                            mode="single" wire:model="birth_date" />
                        <x-ui.error name="birth_date" />
                    </x-ui.field>
                    <x-ui.error name="profile" />
                </div>
                <div class="flex justify-between items-center gap-5">
                    <x-ui.button type="button" wire:click="cancelEdit"
                        class="w-full rounded-lg bg-gradient-to-r from-red-800 to-red-600">Cancel</x-ui.button>
                    <x-ui.button type="submit" wire:target="editProfile"
                        class="w-full rounded-lg bg-gradient-to-r from-slate-800 to-slate-600">Apply</x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.modal>
</div>
