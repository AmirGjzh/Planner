<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'value' => null,
    'label' => null,
    'searchLabel' => null,
    'disabled' => false,
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
    'value' => null,
    'label' => null,
    'searchLabel' => null,
    'disabled' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php foreach ((['height' => 'h-11']) as $__key => $__value) {
    $__consumeVariable = is_string($__key) ? $__key : $__value;
    $$__consumeVariable = is_string($__key) ? $__env->getConsumableComponentData($__key, $__value) : $__env->getConsumableComponentData($__value);
} ?>

<?php
    $value = filled($value) ? $value : trim($slot->__toString());
    $label = filled($label) ? $label : trim($slot->__toString());
    $searchLabel = filled($searchLabel) ? $searchLabel : $label;
?>

<li
    role="option"
    tabindex="0"
    data-value="<?php echo e($value); ?>"
    data-label="<?php echo e($label); ?>"
    data-search="<?php echo e($searchLabel); ?>"
    x-on:click="select($el.dataset.value, $el.dataset.label)"
    x-on:keydown="handleOptionKeydown($event)"
    x-show="!search || $el.dataset.search.toLowerCase().includes(search.toLowerCase())"
    :data-selected="state === $el.dataset.value ? 'true' : 'false'"
    :class="{
        'bg-(--mine-select-selected-bg)': state === $el.dataset.value,
        'text-(--mine-select-selected-text) font-medium': state === $el.dataset.value,
        'hover:bg-(--mine-select-bg-hover) mine-text-primary': state !== $el.dataset.value,
    }"
    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
        'flex items-center rounded-xl px-3 text-sm cursor-pointer transition-colors duration-200 outline-none focus-visible:ring-4 focus-visible:ring-[var(--mine-select-ring-focus)]',
        'py-2.5' => $height === 'h-12',
        $height => $height !== 'h-12',
        'opacity-50 cursor-not-allowed' => $disabled,
    ]); ?>"
    <?php if($disabled): ?> aria-disabled="true" <?php endif; ?>
>
    <div
        :class="state === $el.closest('[role=option]').dataset.value ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
        class="size-4 mr-3 shrink-0 transition-all duration-200"
    >
        <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'check','variant' => 'micro','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check','variant' => 'micro','class' => 'size-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9)): ?>
<?php $attributes = $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9; ?>
<?php unset($__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale972b8dab630a05a3405c81d7f2bc7b9)): ?>
<?php $component = $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9; ?>
<?php unset($__componentOriginale972b8dab630a05a3405c81d7f2bc7b9); ?>
<?php endif; ?>
    </div>

    <span class="">
        <?php echo e($slot); ?>

    </span>
</li>
<?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\resources\views/components/mine/select/option.blade.php ENDPATH**/ ?>