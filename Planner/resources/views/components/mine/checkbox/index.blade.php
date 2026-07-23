<label class="mine-checkbox inline-flex items-center gap-2.5 cursor-pointer">
    <input
        type="checkbox"
        {{ $attributes->whereStartsWith('wire:model') }}
        class="sr-only peer"
    />

    <div
        @class([
            'flex items-center justify-center w-5 h-5 rounded-[6px] border-2 bg-[var(--mine-input-bg)] transition-all duration-200 ease-out shrink-0',
            'border-[var(--mine-input-border)]',
            'peer-checked:bg-[var(--mine-checkbox-bg)] peer-checked:border-[var(--mine-checkbox-bg)]',
            'peer-focus-visible:ring-4 peer-focus-visible:ring-[var(--mine-input-ring-focus)] peer-focus-visible:outline-none',
            '[&_svg]:opacity-0 peer-checked:[&_svg]:opacity-100',
        ])
    >
        <x-mine.icon
            name="check"
            variant="micro"
            class="text-(--mine-checkbox-text)! size-4 transition-all duration-200 ease-out"
        />
    </div>

    <span class="text-sm font-medium mine-text-primary">
        {{ $slot }}
    </span>
</label>
