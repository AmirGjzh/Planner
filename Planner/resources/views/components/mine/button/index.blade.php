@php use Illuminate\View\ComponentAttributeBag; @endphp
@props([
    'type' => 'submit',
    'loading' => false,
    'height' => 'h-11',
])

@php
    $hasWireLoading = filled($attributes->whereStartsWith('wire:loading')->first());

    $loadingAttributes = new ComponentAttributeBag()
        ->merge($hasWireLoading || $type === 'submit' ? [
            'wire:loading.attr' => 'data-loading',
            'wire:target' => $attributes->has('wire:target')
                ? $attributes->get('wire:target')
                : ($attributes->whereStartsWith('wire:click')->first() ?? null),
        ] : []);

    $loadingAttributes = $loadingAttributes->merge($loading ? [
        'data-loading' => 'true',
    ] : []);

    $userHasClass = filled((string) $attributes->get('class'));

    $classes = [
        'relative',
        'inline-flex',
        'items-center',
        'justify-center',
        $height,
        'w-full',
        'rounded-xl',
        'px-4',
        'text-[15px]',
        'font-semibold',
        'active:translate-y-[1px]',
        'transition-all',
        'duration-200',
        'ease-out',
        'cursor-pointer',
        'select-none',
        'whitespace-nowrap',
        'focus-visible:outline-none',
        '[&>[data-loading=true]:first-child]:flex' => true,
        '[&>[data-loading=true]:first-child~*]:opacity-0' => true,
        'btn-primary' => ! $userHasClass,
    ];
@endphp

<button
    type="{{ $type }}"
    @disabled($loading)
    {{ $attributes->class(Arr::toCssClasses($classes)) }}
    aria-busy="{{ $loading ? 'true' : 'false' }}"
>
    <div
        @class(['absolute inset-0 hidden items-center justify-center'])
        {{ $loadingAttributes }}
    >
        <svg
            class="size-5 animate-spin text-(--mine-btn-primary-text)"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
    </div>

    <span>
        {{ $slot }}
    </span>
</button>
