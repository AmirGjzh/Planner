<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label',
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
    'label',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div
    x-data="{ open: false }"
    @click.away="open = false"
    class="relative inline-flex overflow-visible"
>
    <button
        type="button"
        @click="open = !open"
        :data-open="open"
        class="flex items-center gap-1 py-1.5 px-3 rounded-xl border-2 bg-(--mine-input-bg) border-(--mine-input-border)/0 text-sm mine-text-primary font-medium transition-all duration-200 cursor-pointer data-open:border-(--mine-input-border-focus) data-open:ring-2 data-open:ring-(--mine-input-ring-focus) focus-visible:ring-2 focus-visible:ring-(--mine-input-ring-focus) focus-visible:outline-none"
    >
        <span x-text="<?php echo e($label); ?>"></span>
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute top-full mt-1 left-1/2 -translate-x-1/2 z-50 w-max max-h-60 overflow-y-auto mine-scrollbar bg-(--mine-input-bg) rounded-xl border-2 border-(--mine-input-border) shadow-lg p-1"
    >
        <?php echo e($slot); ?>

    </div>
</div>
<?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\resources\views/components/mine/datepicker/select.blade.php ENDPATH**/ ?>