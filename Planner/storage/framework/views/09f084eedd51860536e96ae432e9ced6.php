<?php foreach (([
    'placeholder' => 'Select...',
    'disabled' => false,
    'invalid' => false,
    'height' => 'h-11',
]) as $__key => $__value) {
    $__consumeVariable = is_string($__key) ? $__key : $__value;
    $$__consumeVariable = is_string($__key) ? $__env->getConsumableComponentData($__key, $__value) : $__env->getConsumableComponentData($__value);
} ?>

<div
    data-select-trigger
    x-on:click="toggle()"
    @keydown.enter.prevent="toggle()"
    @keydown.space.prevent="toggle()"
    :data-open="isOpen"
    tabindex="0"
    role="button"
    aria-disabled="<?php if($disabled): ?> true <?php endif; ?>"
    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
        'flex items-center justify-between w-full px-4 rounded-xl border-2 bg-(--mine-input-bg) transition-all duration-200 ease-out cursor-pointer focus-visible:outline-none',
        $height => true,
        'border-(--mine-input-border) data-open:border-(--mine-input-border-focus) focus-visible:border-(--mine-input-border-focus)' => !$invalid,
        'data-open:ring-4 data-open:ring-(--mine-input-ring-focus) focus-visible:ring-4 focus-visible:ring-(--mine-input-ring-focus)' => !$invalid,
        'border-(--mine-input-error-border) ring-4 ring-(--mine-input-error-ring)' => $invalid,
        'opacity-60 cursor-not-allowed' => $disabled,
    ]); ?>"
    <?php echo e($attributes); ?>

>
    <span
        x-text="selectedLabel"
        :class="hasSelection ? 'mine-text-primary' : 'text-(--mine-input-placeholder)'"
        class="text-sm truncate"
    >
        <?php echo e($placeholder); ?>

    </span>

    <div :class="isOpen ? 'rotate-180' : ''" class="shrink-0 transition-transform duration-200 ml-2">
        <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'chevron-up-down','variant' => 'mini','class' => 'text-(--mine-input-icon)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-up-down','variant' => 'mini','class' => 'text-(--mine-input-icon)']); ?>
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
</div>
<?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\resources\views/components/mine/select/trigger.blade.php ENDPATH**/ ?>