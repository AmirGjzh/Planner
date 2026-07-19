@aware([
    'placeholder' => 'Select...',
    'disabled' => false,
    'invalid' => false,
    'height' => 'h-11',
])

<div
    data-select-trigger
    x-on:click="toggle()"
    :data-open="isOpen"
    @class([
        'flex items-center justify-between w-full px-4 rounded-xl border-2 bg-[var(--mine-input-bg)] transition-all duration-200 ease-out cursor-pointer',
        $height => true,
        'border-[var(--mine-input-border)] data-open:border-[var(--mine-input-border-focus)]' => !$invalid,
        'data-open:ring-4 data-open:ring-[var(--mine-input-ring-focus)]' => !$invalid,
        'border-[var(--mine-input-error-border)] ring-4 ring-[var(--mine-input-error-ring)]' => $invalid,
        'opacity-60 cursor-not-allowed' => $disabled,
    ])
    {{ $attributes }}
>
    <span
        x-text="selectedLabel"
        :class="hasSelection ? 'mine-text-primary' : 'text-[var(--mine-input-placeholder)]'"
        class="text-sm truncate"
    >
        {{ $placeholder }}
    </span>

    <div :class="isOpen ? 'rotate-180' : ''" class="shrink-0 transition-transform duration-200 ml-2">
        <x-mine.icon
            name="chevron-up-down"
            variant="mini"
            class="text-[var(--mine-input-icon)]"
        />
    </div>
</div>
