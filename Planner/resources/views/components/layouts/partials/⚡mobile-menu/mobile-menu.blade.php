<div>
    <x-ui.modal bare id="mobile-menu" slideover>
        <div class="h-full p-2 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center px-4">
                    <x-ui.brand class="text-slate-600" href="{{ route('dashboard') }}" name="Planner" />
                    <x-ui.icon x-on:click="$data.close()" name="bars-3"
                        class="size-8 opacity-80 sm:my-2 hover:cursor-pointer" />
                </div>
                <x-ui.separator class="mt-4 mb-8 px-4" />
            </div>

            <x-ui.navlist class="flex-1">
                <livewire:layouts.partials.nav-links variant="navlist" />
            </x-ui.navlist>

            @if ($auth)
                <div class="mx-2 flex flex-col gap-2 p-2">
                    <x-ui.button href="{{ route('profile') }}" class="bg-slate-500 rounded-lg" wire:navigate>
                        Profile
                    </x-ui.button>
                    <x-ui.button class="bg-gradient-to-r from-red-800 to-red-600 rounded-lg" x-on:click="
                            fetch('{{ route('logout') }}', {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            }).then(() => Livewire.navigate('{{ route('login') }}'));
                        ">
                        Logout
                    </x-ui.button>
                </div>
            @else
                <div class="mx-2 flex flex-col gap-2 p-2">
                    <x-ui.button href="{{ route('login') }}" class="bg-slate-500 rounded-lg" wire:navigate>
                        Sign in
                    </x-ui.button>
                    <x-ui.button href="{{ route('register') }}" class="bg-slate-700 rounded-lg" wire:navigate>
                        Register
                    </x-ui.button>
                </div>
            @endif
        </div>
    </x-ui.modal>
</div>
