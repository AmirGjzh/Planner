<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'placement' => 'bottom-end',
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
    'placement' => 'bottom-end',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$placements = [
    'bottom-start' => 'left-0 top-full mt-0 origin-top-left',
    'bottom-end' => 'right-0 top-full mt-0 origin-top-right',
    'top-start' => 'left-0 bottom-full mb-0 origin-bottom-left',
    'top-end' => 'right-0 bottom-full mb-0 origin-bottom-right',
];
$position = $placements[$placement] ?? $placements['bottom-end'];
?>

<div
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    x-cloak
    @mouseenter="cancelHide()"
    @mouseleave="hide(150)"
    @keydown.down.prevent="focusNext()"
    @keydown.up.prevent="focusPrev()"
    @keydown.home.prevent="focusFirst()"
    @keydown.end.prevent="focusLast()"
    <?php echo e($attributes->merge([
        'class' => '
            absolute
            z-50
            min-w-full '.
            $position.
            ' bg-(--mine-dropdown-bg)
            rounded-xl
            border-2
            border-(--mine-dropdown-border)
            shadow-lg
            p-1
        ',
    ])); ?>

>
    <?php echo e($slot); ?>

</div>
<?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\resources\views/components/mine/dropdown/content.blade.php ENDPATH**/ ?>