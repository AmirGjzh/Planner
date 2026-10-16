@props([
    'name' => $attributes->whereStartsWith('wire:model')->first() ?? $attributes->whereStartsWith('x-model')->first(),
    'label' => null,
    'type' => 'text',
    'placeholder' => null,
    'required' => false,
    'leftIcon' => null,
    'height' => 'h-11',
])

<div class="mine-input w-full">
    @if ($label)
        <x-mine.input.label :for="$name" :required="$required"> {{ $label }} </x-mine.input.label>
    @endif

    <div
        @if ($type === 'password') x-data="{ show: false }" @endif
        @class([
            'group flex w-full items-center overflow-hidden rounded-xl border-2 transition-[border-color,background-color,box-shadow] duration-200 ease-out',
            $height => true,
            'bg-[var(--mine-input-bg)]' => ! $errors->has($name),
            'bg-[var(--mine-input-error-bg)]' => $errors->has($name),
            'border-[var(--mine-input-border)]' => ! $errors->has($name),
            'border-[var(--mine-input-error-border)]' => $errors->has($name),
            'focus-within:border-[var(--mine-input-border-focus)] focus-within:ring-4 focus-within:ring-[var(--mine-input-ring-focus)]' => ! $errors->has($name),
            'focus-within:border-[var(--mine-input-error-border)] focus-within:ring-[var(--mine-input-error-ring)] focus-within:ring-4' => $errors->has($name),
        ])
        @if ($errors->has($name))
            style="
                --mine-input-autofill-bg: var(--mine-input-error-bg);
                --mine-input-placeholder: var(--mine-input-error-placeholder);
            "
        @endif
    >
        @if ($leftIcon)
            <div @class([
                'pl-4' => app()->isLocale('en'),
                'pr-4' => app()->isLocale('fa'),
                'flex h-full items-center',
                'text-(--mine-input-icon)' => ! $errors->has($name),
                'text-(--mine-input-error-icon)' => $errors->has($name),
                'group-focus-within:text-(--mine-input-border-focus)' => ! $errors->has($name),
                'group-focus-within:text-(--mine-input-error-border)' => $errors->has($name),
            ])>
                <x-mine.icon :name="$leftIcon" size="20" weight="filled" />
            </div>
        @endif

        <input
            id="{{ $name }}"
            name="{{ $name }}"
            @if (! $label && $placeholder && ! $attributes->has('aria-label')) aria-label="{{ $placeholder }}" @endif
            @if ($type === 'password') :type="show ? 'text' : 'password'" @else type="{{ $type }}" @endif
            placeholder="{{ $placeholder }}"
            @if ($errors->has($name))
                aria-invalid="true"
                aria-describedby="{{ $name }}-error"
            @endif
            {{
                $attributes->except('class')->merge([
                    'class' => (app()->isLocale('en') ? 'pt-1 ' : 'pt-1 placeholder:mb-1 ').
                    'h-full
                w-full
                bg-transparent
                px-4
                text-sm
                font-medium
                text-[var(--mine-text-primary)]/80
                placeholder:font-medium
                placeholder:text-[var(--mine-input-placeholder)]
                outline-none',
                ])
            }}
        />

        @if ($type === 'password')
            <button
                type="button"
                aria-label="{{ __('Show password') }}"
                :aria-pressed="show"
                @click="show = ! show"
                @class([
                    'pr-4' => app()->isLocale('en'),
                    'pl-4' => app()->isLocale('fa'),
                    'flex h-full items-center hover:cursor-pointer transition-colors duration-200 focus-visible:outline-none',
                    'text-(--mine-input-icon)' => ! $errors->has($name),
                    'text-(--mine-input-error-icon)' => $errors->has($name),
                    'group-focus-within:text-(--mine-input-border-focus)' => ! $errors->has($name),
                    'group-focus-within:text-(--mine-input-error-border)' => $errors->has($name),
                    'focus-visible:ring-4 focus-visible:ring-(--mine-input-ring-focus)' => ! $errors->has($name),
                    'focus-visible:ring-4 focus-visible:ring-(--mine-input-error-ring)' => $errors->has($name),
                ])
            >
                <template x-if="! show">
                    <x-mine.icon name="EyeClosed" size="20" weight="filled" />
                </template>
                <template x-if="show">
                    <x-mine.icon name="EyeOpen" size="20" weight="filled" />
                </template>
            </button>
        @endif
    </div>

    <x-mine.input.error :name="$name" />
</div>
