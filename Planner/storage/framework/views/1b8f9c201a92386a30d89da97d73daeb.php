<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'as' => 'button',
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
    'as' => 'button',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($as === 'button'): ?>

<button
    type="button"
    x-ref="trigger"
    @mouseenter="show()"
    @mouseleave="hide(150)"
    @click.stop="toggle()"
    @keydown.enter.prevent="toggle()"
    @keydown.space.prevent="toggle()"
    :aria-expanded="open"
    aria-haspopup="menu"
    <?php echo e($attributes->merge([
        'class' => '
            inline-flex
            items-center
            justify-center
            rounded-xl
            focus-visible:ring-4
            focus-visible:ring-(--mine-input-ring-focus)
            focus-visible:outline-none
            transition-all
            duration-200
            ease-out
        ',
    ])); ?>

>
    <?php echo e($slot); ?>

</button>

<?php else: ?>

<div
    x-ref="trigger"
    @mouseenter="show()"
    @mouseleave="hide(150)"
    @click.stop="toggle()"
    @keydown.enter.prevent="toggle()"
    @keydown.space.prevent="toggle()"
    tabindex="0"
    role="button"
    :aria-expanded="open"
    aria-haspopup="menu"
    <?php echo e($attributes->merge([
        'class' => '
            inline-flex
            items-center
            justify-center
            cursor-pointer
            rounded-xl
            focus-visible:ring-4
            focus-visible:ring-(--mine-input-ring-focus)
            focus-visible:outline-none
            transition-all
            duration-200
            ease-out
            flex-shrink-0
            whitespace-nowrap
        ',
    ])); ?>

>
    <?php echo e($slot); ?>

</div>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\resources\views/components/mine/dropdown/trigger.blade.php ENDPATH**/ ?>