<div>
    <x-ui.dropdown>
        <x-slot:button class="bg-slate-100 p-1 rounded-lg mr-1">
            <x-ui.icon name="user-circle" class="size-8" />
        </x-slot:button>
        <x-slot:menu>
            <x-ui.dropdown.item wire:navigate href="{{ route('profile') }}" icon="user">
                Profile
            </x-ui.dropdown.item>
            <x-ui.dropdown.item variant="danger" icon="arrow-left-start-on-rectangle" x-on:click="
                fetch('{{ route('logout') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                }).then(() => Livewire.navigate('{{ route('login') }}'));
            ">
                Log out
            </x-ui.dropdown.item>
        </x-slot:menu>
    </x-ui.dropdown>
</div>
