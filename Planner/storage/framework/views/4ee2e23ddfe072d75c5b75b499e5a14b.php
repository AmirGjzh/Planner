<label class="mine-checkbox inline-flex items-center gap-2.5 cursor-pointer">
    <input
        type="checkbox"
        <?php echo e($attributes->whereStartsWith('wire:model')); ?>

        class="sr-only peer"
    />

    <div
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'flex items-center justify-center w-5 h-5 rounded-[6px] border-2 bg-[var(--mine-input-bg)] transition-all duration-200 ease-out shrink-0',
            'border-[var(--mine-input-border)]',
            'peer-checked:bg-[var(--mine-checkbox-bg)] peer-checked:border-[var(--mine-checkbox-bg)]',
            'peer-focus-visible:ring-4 peer-focus-visible:ring-[var(--mine-input-ring-focus)] peer-focus-visible:outline-none',
            '[&_svg]:opacity-0 peer-checked:[&_svg]:opacity-100',
        ]); ?>"
    >
        <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'check','variant' => 'micro','class' => 'text-(--mine-checkbox-text)! size-4 transition-all duration-200 ease-out']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check','variant' => 'micro','class' => 'text-(--mine-checkbox-text)! size-4 transition-all duration-200 ease-out']); ?>
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

    <span class="text-sm font-medium mine-text-primary">
        <?php echo e($slot); ?>

    </span>
</label>
<?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\resources\views/components/mine/checkbox/index.blade.php ENDPATH**/ ?>