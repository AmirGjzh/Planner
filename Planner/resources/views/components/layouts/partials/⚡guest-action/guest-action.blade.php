<div class="flex gap-2">
    <x-ui.button href="{{ route('login') }}" class="bg-slate-500 rounded-lg" wire:navigate>
        Sign in
    </x-ui.button>
    <x-ui.button href="{{ route('register') }}" class="bg-slate-700 rounded-lg" wire:navigate>
        Register
    </x-ui.button>
</div>
