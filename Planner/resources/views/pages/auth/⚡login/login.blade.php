<div>
    <form wire:submit.prevent="login">
        <div>
            <label>Email</label>
            <input type="email" wire:model="email">
            @error('email') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label>Password</label>
            <input type="password" wire:model="password">
            @error('password') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label>
                <input type="checkbox" wire:model="remember">
                Remember me
            </label>
        </div>

        <x-ui.button type="submit">
            Button
        </x-ui.button>
    </form>
</div>
