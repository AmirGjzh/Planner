<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'href' => '#',
    'route' => null,
    'active' => false,
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
    'href' => '#',
    'route' => null,
    'active' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$active = $active || ($route && request()->routeIs($route));
?>

<a
    wire:navigate.hover
    href="<?php echo e($href); ?>"
    class="<?php echo e($active ? 'mine-nav-link-active' : 'mine-nav-link'); ?>"
>
    <?php echo e($slot); ?>

</a>
<?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\resources\views/components/mine/nav-link/index.blade.php ENDPATH**/ ?>