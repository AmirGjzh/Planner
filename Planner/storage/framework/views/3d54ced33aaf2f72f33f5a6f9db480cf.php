<?php use Illuminate\View\ComponentAttributeBag; ?>
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type' => 'submit',
    'loading' => false,
    'height' => 'h-11',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'type' => 'submit',
    'loading' => false,
    'height' => 'h-11',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
        'transition',
        'duration-200',
        'ease-out',
        'cursor-pointer',
        'select-none',
        'whitespace-nowrap',
        'focus-visible:outline-none',
        '[&>[data-loading=true]:first-child]:flex' => true,
        '[&>[data-loading=true]:first-child~*]:invisible' => true,
        'mine-btn-primary' => ! $userHasClass,
    ];
?>

<button
    type="<?php echo e($type); ?>"
    <?php if($loading): echo 'disabled'; endif; ?>
    <?php echo e($attributes->class(Arr::toCssClasses($classes))); ?>

    aria-busy="<?php echo e($loading ? 'true' : 'false'); ?>"
>
    <div
        class="<?php echo \Illuminate\Support\Arr::toCssClasses(['absolute inset-0 hidden items-center justify-center']); ?>"
        <?php echo e($loadingAttributes); ?>

    >
        <svg
            class="size-5 animate-spin text-current"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
    </div>

    <span>
        <?php echo e($slot); ?>

    </span>
</button>
<?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\resources\views/components/mine/button/index.blade.php ENDPATH**/ ?>