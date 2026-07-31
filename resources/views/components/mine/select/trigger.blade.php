@aware([
    'placeholder' => 'Select...',
    'disabled' => false,
    'invalid' => false,
    'height' => 'h-11',
])

<div
    data-select-trigger
    x-on:click="toggle()"
    @keydown.enter.prevent="toggle()"
    @keydown.space.prevent="toggle()"
    :data-open="isOpen"
    tabindex="0"
    role="button"
    aria-disabled="@if($disabled) true @endif"
    @class([
        'flex items-center justify-between w-full px-4 rounded-xl border-2 bg-(--mine-input-bg) transition-all duration-200 ease-out cursor-pointer focus-visible:outline-none',
        $height => true,
        'border-(--mine-input-border) data-open:border-(--mine-input-border-focus) focus-visible:border-(--mine-input-border-focus)' => !$invalid,
        'data-open:ring-4 data-open:ring-(--mine-input-ring-focus) focus-visible:ring-4 focus-visible:ring-(--mine-input-ring-focus)' => !$invalid,
        'border-(--mine-input-error-border) ring-4 ring-(--mine-input-error-ring)' => $invalid,
        'opacity-60 cursor-not-allowed' => $disabled,
    ])
    {{ $attributes }}
>
    <span
        x-text="selectedLabel"
        :class="hasSelection ? 'mine-text-primary' : 'text-(--mine-input-placeholder)'"
        class="text-sm truncate"
    >
        {{ $placeholder }}
    </span>

    <div :class="isOpen ? 'rotate-180' : ''" class="shrink-0 transition-transform duration-200 ml-2">
        <x-mine.icon
            name="chevron-up-down"
            variant="mini"
            class="text-(--mine-input-icon)"
        />
    </div>
</div>
