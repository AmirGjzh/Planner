<label class="mine-checkbox inline-flex cursor-pointer items-center gap-2">
    <input type="checkbox" {{ $attributes->whereStartsWith('wire:model') }} class="peer sr-only" />

    <div @class([
        'flex items-center justify-center w-5 h-5 rounded-md border-2 bg-[var(--mine-input-bg)] transition-all duration-200 ease-out shrink-0 mb-1',
        'border-[var(--mine-input-border)]',
        'peer-checked:bg-[var(--mine-checkbox-bg)] peer-checked:border-[var(--mine-checkbox-bg)]',
        'peer-focus-visible:ring-4 peer-focus-visible:ring-[var(--mine-input-ring-focus)] peer-focus-visible:outline-none',
        '[&_svg]:opacity-0 peer-checked:[&_svg]:opacity-100',
    ])>
        <x-mine.icon
            name="Check"
            size="12"
            weight="filled"
            class="text-(--mine-checkbox-text)! transition-all duration-200 ease-out"
        />
    </div>

    <span class="mine-text-primary text-sm font-medium"> {{ $slot }} </span>
</label>
