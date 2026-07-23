@props([
    'label' => null,
    'vertical' => false,
])

@if ($vertical)

    @if ($label)
        <div
            class="flex flex-col items-center self-stretch mx-2 gap-2 w-6 transition-colors duration-300"
            role="separator"
            aria-orientation="vertical"
            aria-label="{{ $label }}"
        >
            <div class="flex-1 w-px bg-(--mine-separator-border)" aria-hidden="true"></div>

            <span class="text-sm font-medium mine-text-secondary whitespace-nowrap select-none">
                {{ $label }}
            </span>

            <div class="flex-1 w-px bg-(--mine-separator-border)" aria-hidden="true"></div>
        </div>
    @else
        <div
            class="mine-separator w-px self-stretch shrink-0 min-h-[1em] bg-(--mine-separator-border)"
            role="separator"
            aria-orientation="vertical"
        ></div>
    @endif

@elseif($label)

<div
    class="flex items-center w-full gap-4 h-6" transition-colors duration-300
    role="separator"
    aria-orientation="horizontal"
    aria-label="{{ $label }}"
>
    <div class="flex-1 h-px bg-(--mine-separator-border)" aria-hidden="true"></div>

    <span class="text-sm font-medium mine-text-secondary whitespace-nowrap select-none">
        {{ $label }}
    </span>

    <div class="flex-1 h-px bg-(--mine-separator-border)" aria-hidden="true"></div>
</div>

@else

<div
    class="mine-separator w-full h-px bg-(--mine-separator-border)"
    role="separator"
    aria-orientation="horizontal"
></div>

@endif
