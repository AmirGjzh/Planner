<div class="m-2 mine-card overflow-hidden">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 px-8 py-10">
        <div class="flex flex-col gap-3">
            <div class="flex items-center gap-2">
                <x-mine.icon name="calendar-days" class="size-5" variant="solid" />
                <h1 class="text-base font-semibold mine-text-primary">Planner</h1>
            </div>
            <p class="mine-text-secondary text-sm leading-relaxed">
                Organize your tasks, plans, and deadlines in one place. Built for clarity and focus.
            </p>
        </div>

        <div class="flex flex-col gap-3">
            <h2 class="text-sm font-semibold mine-text-primary uppercase tracking-wider">Quick Links</h2>
            <div class="flex flex-col gap-2">
                <a href="{{ route('dashboard') }}" class="mine-text-secondary text-sm hover:text-(--mine-text-link) transition-colors duration-150">Dashboard</a>
                <a href="{{ route('tasks') }}" class="mine-text-secondary text-sm hover:text-(--mine-text-link) transition-colors duration-150">Tasks</a>
                <a href="{{ route('plans') }}" class="mine-text-secondary text-sm hover:text-(--mine-text-link) transition-colors duration-150">Plans</a>
                <a href="{{ route('categories') }}" class="mine-text-secondary text-sm hover:text-(--mine-text-link) transition-colors duration-150">Categories</a>
                <a href="{{ route('report-page') }}" class="mine-text-secondary text-sm hover:text-(--mine-text-link) transition-colors duration-150">Reports</a>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <h2 class="text-sm font-semibold mine-text-primary uppercase tracking-wider">Account</h2>
            <div class="flex flex-col gap-2">
                <a href="{{ route('profile') }}" class="mine-text-secondary text-sm hover:text-(--mine-text-link) transition-colors duration-150">Profile</a>
                <a href="#" class="mine-text-secondary text-sm hover:text-(--mine-text-link) transition-colors duration-150">Settings</a>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <h2 class="text-sm font-semibold mine-text-primary uppercase tracking-wider">Tech Stack</h2>
            <div class="flex flex-col gap-2">
                <span class="mine-text-secondary text-sm flex items-center gap-2">
                    <x-mine.icon name="bolt" class="size-3.5" /> Laravel 13
                </span>
                <span class="mine-text-secondary text-sm flex items-center gap-2">
                    <x-mine.icon name="sparkles" class="size-3.5" /> Livewire 4
                </span>
                <span class="mine-text-secondary text-sm flex items-center gap-2">
                    <x-mine.icon name="swatch" class="size-3.5" /> Tailwind CSS v4
                </span>
            </div>
        </div>
    </div>

    <div class="border-t border-(--mine-separator-border) px-8 py-4 flex flex-col sm:flex-row justify-between items-center gap-2">
        <p class="mine-text-secondary text-xs">&copy; {{ date('Y') }} Planner. All rights reserved.</p>
        <p class="mine-text-secondary text-xs">Made with care.</p>
    </div>
</div>
