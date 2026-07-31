<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label' => null,
    'vertical' => false,
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
    'label' => null,
    'vertical' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vertical): ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($label): ?>
        <div
            class="flex flex-col items-center self-stretch mx-2 gap-2 w-6 transition-colors duration-300"
            role="separator"
            aria-orientation="vertical"
            aria-label="<?php echo e($label); ?>"
        >
            <div class="flex-1 w-px bg-(--mine-separator-border)" aria-hidden="true"></div>

            <span class="text-sm font-medium mine-text-secondary whitespace-nowrap select-none">
                <?php echo e($label); ?>

            </span>

            <div class="flex-1 w-px bg-(--mine-separator-border)" aria-hidden="true"></div>
        </div>
    <?php else: ?>
        <div
            class="mine-separator w-px self-stretch shrink-0 min-h-[1em] bg-(--mine-separator-border)"
            role="separator"
            aria-orientation="vertical"
        ></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php elseif($label): ?>

<div
    class="flex items-center w-full gap-4 h-6" transition-colors duration-200
    role="separator"
    aria-orientation="horizontal"
    aria-label="<?php echo e($label); ?>"
>
    <div class="flex-1 h-px bg-(--mine-separator-border)" aria-hidden="true"></div>

    <span class="text-sm font-medium mine-text-secondary whitespace-nowrap select-none">
        <?php echo e($label); ?>

    </span>

    <div class="flex-1 h-px bg-(--mine-separator-border)" aria-hidden="true"></div>
</div>

<?php else: ?>

<div
    class="mine-separator w-full h-px bg-(--mine-separator-border)"
    role="separator"
    aria-orientation="horizontal"
></div>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\resources\views/components/mine/separator/index.blade.php ENDPATH**/ ?>