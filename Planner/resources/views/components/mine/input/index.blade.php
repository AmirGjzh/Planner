@props([
    'name' => $attributes->whereStartsWith('wire:model')->first() ?? $attributes->whereStartsWith('x-model')->first(),
    'label' => null,
    'type' => 'text',
    'placeholder' => null,
    'helper' => null,
    'required' => false,
    'leftIcon' => null,
    'height' => 'h-11',
])

<style>
    .mine-input input::-ms-reveal,
    .mine-input input::-ms-clear {
        display: none;
    }
    .mine-input input:-webkit-autofill,
    .mine-input input:-webkit-autofill:hover,
    .mine-input input:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0 1000px var(--mine-input-autofill-bg, var(--mine-input-bg)) inset !important;
        -webkit-text-fill-color: var(--mine-text-primary) !important;
        caret-color: var(--mine-text-primary) !important;
    }
</style>

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
        @if($type === 'password') x-data="{ show: false }" @endif
        @class([
            'group flex w-full items-center overflow-hidden rounded-xl border-2 transition-all duration-200 ease-out',
            $height => true,
            'bg-[var(--mine-input-bg)]' => !$errors->has($name),
            'bg-[var(--mine-input-error-bg)]' => $errors->has($name),
            'border-[var(--mine-input-border)]' => !$errors->has($name),
            'border-[var(--mine-input-error-border)]' => $errors->has($name),
            'focus-within:border-[var(--mine-input-border-focus)] focus-within:ring-4 focus-within:ring-[var(--mine-input-ring-focus)]' => !$errors->has($name),
            'focus-within:border-[var(--mine-input-error-border)] focus-within:ring-[var(--mine-input-error-ring)] focus-within:ring-4' => $errors->has($name)
        ])
        @if($errors->has($name)) style="--mine-input-autofill-bg: var(--mine-input-error-bg)" @endif
    >
        @if($leftIcon)
            <div class="flex h-full items-center pl-4 text-(--mine-input-icon) group-focus-within:text-(--mine-input-border-focus)">
                <x-mine.icon variant="solid" :name="$leftIcon" />
            </div>
        @endif

        <input
            id="{{ $name }}"
            name="{{ $name }}"
            @if($type === 'password') :type="show ? 'text' : 'password'" @else type="{{ $type }}" @endif
            placeholder="{{ $placeholder }}"
            {{ $attributes->except('class')->merge([
                'class' => '
                    h-full
                    w-full
                    bg-transparent
                    px-4
                    text-[15px]
                    mine-text-primary
                    placeholder:text-[var(--mine-input-placeholder)]
                    outline-none
                '
            ]) }}
        >

        @if($type === 'password')
            <button
                type="button"
                @click="show = !show"
                tabindex="-1"
                class="flex h-full items-center pr-4 text-(--mine-input-icon) group-focus-within:text-(--mine-input-border-focus) hover:cursor-pointer transition-colors duration-200"
            >
                <template x-if="!show">
                    <x-mine.icon variant="solid" name="eye-slash" />
                </template>
                <template x-if="show">
                    <x-mine.icon variant="solid" name="eye" />
                </template>
            </button>
        @endif
    </div>

    <x-mine.input.error
        :name="$name"
    />
</div>
