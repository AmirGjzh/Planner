@props([
    'name' => $attributes->whereStartsWith('wire:model')->first() ?? $attributes->whereStartsWith('x-model')->first(),
    'label' => null,
    'placeholder' => null,
    'helper' => null,
    'required' => false,
    'rows' => 4,
])

<div class="mine-input w-full">
    @if($label)
        <x-mine.input.label
            :for="$name"
            :required="$required"
        >
            {{ $label }}
        </x-mine.input.label>
    @endif

    <div
        @class([
            'group flex w-full overflow-hidden rounded-xl border-2 transition-all duration-200 ease-out',
            'bg-[var(--mine-input-bg)]' => !$errors->has($name),
            'bg-[var(--mine-input-error-bg)]' => $errors->has($name),
            'border-[var(--mine-input-border)]' => !$errors->has($name),
            'border-[var(--mine-input-error-border)] ring-4 ring-[var(--mine-input-error-ring)]' => $errors->has($name),
            'focus-within:border-[var(--mine-input-border-focus)] focus-within:ring-4 focus-within:ring-[var(--mine-input-ring-focus)]' => !$errors->has($name),
            'focus-within:border-[var(--mine-input-error-border)] focus-within:ring-[var(--mine-input-error-ring)] focus-within:ring-4' => $errors->has($name)
        ])
        @if($errors->has($name)) style="--mine-input-autofill-bg: var(--mine-input-error-bg)" @endif
    >
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->except('class')->merge([
                'class' => '
                    w-full
                    bg-transparent
                    px-4
                    py-3
                    text-[15px]
                    mine-text-primary
                    placeholder:text-[var(--mine-input-placeholder)]
                    outline-none
                    min-h-[80px]
                    resize-none
                '
            ]) }}
        >{{ old($name) ?? $slot }}</textarea>
    </div>

    <x-mine.input.error
        :name="$name"
    />
</div>
