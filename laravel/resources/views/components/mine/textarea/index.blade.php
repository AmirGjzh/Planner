@props([
    'name' => $attributes->whereStartsWith('wire:model')->first() ?? $attributes->whereStartsWith('x-model')->first(),
    'label' => null,
    'placeholder' => null,
    'required' => false,
    'rows' => 4,
    'leftIcon' => null,
])

<div class="mine-input w-full">
    @if ($label)
        <x-mine.input.label :for="$name" :required="$required"> {{ $label }} </x-mine.input.label>
    @endif

    <div
        @class([
            'group flex w-full items-start overflow-hidden rounded-xl border-2 transition-[border-color,background-color,box-shadow] duration-200 ease-out',
            'bg-[var(--mine-input-bg)]' => ! $errors->has($name),
            'bg-[var(--mine-input-error-bg)]' => $errors->has($name),
            'border-[var(--mine-input-border)]' => ! $errors->has($name),
            'border-[var(--mine-input-error-border)]' => $errors->has($name),
            'focus-within:border-[var(--mine-input-border-focus)] focus-within:ring-4 focus-within:ring-[var(--mine-input-ring-focus)]' => ! $errors->has($name),
            'focus-within:border-[var(--mine-input-error-border)] focus-within:ring-[var(--mine-input-error-ring)] focus-within:ring-4' => $errors->has($name),
        ])
        @if ($errors->has($name)) style="--mine-input-autofill-bg: var(--mine-input-error-bg)" @endif
    >
        @if ($leftIcon)
            <div @class([
                'mt-3',
                'pl-4' => app()->isLocale('en'),
                'pr-4' => app()->isLocale('fa'),
                'flex h-full items-start',
                'text-(--mine-input-icon)' => ! $errors->has($name),
                'text-(--mine-input-error-icon)' => $errors->has($name),
                'group-focus-within:text-(--mine-input-border-focus)' => ! $errors->has($name),
                'group-focus-within:text-(--mine-input-error-border)' => $errors->has($name),
            ])>
                <x-mine.icon :name="$leftIcon" size="20" weight="filled" />
            </div>
        @endif

        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            @if ($errors->has($name))
                aria-invalid="true"
                aria-describedby="{{ $name }}-error"
            @endif
            {{
                $attributes->except('class')->merge([
                    'class' => '
                w-full
                flex-1
                bg-transparent
                px-4
                py-3
                text-sm
                font-medium
                text-[var(--mine-text-primary)]/80
                placeholder:font-medium
                placeholder:text-[var(--mine-input-placeholder)]
                outline-none
                min-h-[80px]
                resize-none
                ',
                ])
            }}
        >{{ old($name) ?? $slot }}</textarea>
    </div>

    <x-mine.input.error :name="$name" />
</div>
