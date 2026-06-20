<div>
    <h1>Dashboard</h1>

    <p>Welcome, {{ auth()->user()->name }}</p>

    <button wire:click="logout">
        Logout
    </button>
</div>
