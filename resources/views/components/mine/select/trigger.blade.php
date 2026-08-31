@aware([
    'placeholder' => 'Select...',
    'disabled' => false,
    'invalid' => false,
    'height' => 'h-11',
    'leftIcon' => null,
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
        'group flex items-center justify-between w-full px-4 rounded-xl border-2 transition-all duration-200 ease-out cursor-pointer focus-visible:outline-none',
        $height => true,
        'bg-(--mine-input-bg)' => !$invalid,
        'bg-(--mine-input-error-bg)' => $invalid,
        'border-(--mine-input-border)' => !$invalid,
        'border-(--mine-input-error-border)' => $invalid,
        'data-open:border-(--mine-input-border-focus) focus-visible:border-(--mine-input-border-focus)' => !$invalid,
        'data-open:border-(--mine-input-error-border) focus-visible:border-(--mine-input-error-border)' => $invalid,
        'data-open:ring-4 data-open:ring-(--mine-input-ring-focus)' => !$invalid,
        'data-open:ring-4 data-open:ring-(--mine-input-error-ring)' => $invalid,
        'focus-visible:ring-4 focus-visible:ring-(--mine-input-ring-focus)' => !$invalid,
        'focus-visible:ring-4 focus-visible:ring-(--mine-input-error-ring)' => $invalid,
        'opacity-60 cursor-not-allowed' => $disabled,
    ])
    {{ $attributes }}
>
    <div class="flex items-center min-w-0">
        @if($leftIcon)
            <div @class([
                'shrink-0',
                'pr-3' => app()->isLocale('en'),
                'pl-3' => app()->isLocale('fa'),
                'flex h-full items-center',
                'text-(--mine-input-icon)' => !$invalid,
                'text-(--mine-input-error-icon)' => $invalid,
                'group-data-open:text-(--mine-input-border-focus)' => !$invalid,
                'group-data-open:text-(--mine-input-error-border)' => $invalid,
            ])>
                <x-mine.icon :name="$leftIcon" size="20" weight="filled" />
            </div>
        @endif

        <span
            x-text="selectedLabel"
            :class="hasSelection ? 'mine-text-primary' : 'text-(--mine-input-placeholder)'"
            class="text-sm truncate pt-1"
        >
            {{ $placeholder }}
        </span>
    </div>

    <div :class="isOpen ? 'rotate-180' : ''"
        @class([
            "ml-2 -mr-1" => app()->isLocale('en'),
            "mr-2 -ml-1" => app()->isLocale('fa'),
            "shrink-0 transition-transform duration-200"
        ])
    >
        <x-mine.icon
            name="ChevronExpandY"
            weight="filled"
            size="16"
            @class([
                'text-(--mine-input-icon)' => !$invalid,
                'text-(--mine-input-error-icon)' => $invalid,
            ])
        />
    </div>
</div>
