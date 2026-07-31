<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'href' => null,
    'route' => null,
    'disabled' => false,
    'destructive' => false,
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
    'href' => null,
    'route' => null,
    'disabled' => false,
    'destructive' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php $href = $route ? route($route) : $href; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($href): ?>
<a
    href="<?php echo e($href); ?>"
    wire:navigate.hover
    role="menuitem"
    tabindex="0"
    @click="close()"
    <?php echo e($attributes->class([
        'flex w-full items-center rounded-lg transition-all duration-200 ease-out outline-none no-underline text-sm mine-text-primary',
        'hover:bg-(--mine-dropdown-item-bg-hover) focus:bg-(--mine-dropdown-item-bg-hover) focus-visible:ring-4 focus-visible:ring-(--mine-dropdown-item-ring)'
            => ! $destructive,
        'hover:bg-(--mine-dropdown-destructive-item-bg-hover) focus:bg-(--mine-dropdown-destructive-item-bg-hover) focus-visible:ring-4 focus-visible:ring-(--mine-dropdown-destructive-item-ring) text-(--mine-dropdown-item-text-danger)'
            => $destructive,
        'cursor-not-allowed opacity-50'
            => $disabled,
    ])); ?>

>
    <?php echo e($slot); ?>

</a>
<?php else: ?>
<button
    type="button"
    role="menuitem"
    tabindex="0"
    <?php if($disabled): echo 'disabled'; endif; ?>
    @keydown.enter.prevent="$el.firstElementChild?.dispatchEvent(new MouseEvent('click', { bubbles: true }))"
    @keydown.space.prevent="$el.firstElementChild?.dispatchEvent(new MouseEvent('click', { bubbles: true }))"
    @click="close()"
    <?php echo e($attributes->class([
        'flex w-full items-center rounded-lg transition-all duration-200 ease-out outline-none text-sm mine-text-primary',
        'hover:bg-(--mine-dropdown-item-bg-hover) focus:bg-(--mine-dropdown-item-bg-hover) focus-visible:ring-4 focus-visible:ring-(--mine-dropdown-item-ring)'
            => ! $destructive,
        'hover:bg-(--mine-dropdown-destructive-item-bg-hover) focus:bg-(--mine-dropdown-destructive-item-bg-hover) focus-visible:ring-4 focus-visible:ring-(--mine-dropdown-destructive-item-ring) text-(--mine-dropdown-item-text-danger)'
            => $destructive,
        'cursor-not-allowed opacity-50'
            => $disabled,
    ])); ?>

>
    <?php echo e($slot); ?>

</button>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\resources\views/components/mine/dropdown/item.blade.php ENDPATH**/ ?>