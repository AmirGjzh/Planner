<div class="m-2 mine-card overflow-hidden">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 px-8 py-10">
        <div class="flex flex-col gap-3">
            <div class="flex items-center gap-2">
                <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'calendar-days','class' => 'size-5','variant' => 'solid']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'calendar-days','class' => 'size-5','variant' => 'solid']); ?>
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
                <h1 class="text-base font-semibold mine-text-primary">Planner</h1>
            </div>
            <p class="mine-text-secondary text-sm leading-relaxed">
                Organize your tasks, plans, and deadlines in one place. Built for clarity and focus.
            </p>
        </div>

        <div class="flex flex-col gap-3">
            <h2 class="text-sm font-semibold mine-text-primary uppercase tracking-wider">Quick Links</h2>
            <div class="flex flex-col gap-2">
                <a href="<?php echo e(route('dashboard')); ?>" class="mine-text-secondary text-sm hover:text-(--mine-text-link) transition-colors duration-150">Dashboard</a>
                <a href="<?php echo e(route('task-page')); ?>" class="mine-text-secondary text-sm hover:text-(--mine-text-link) transition-colors duration-150">Tasks</a>
                <a href="<?php echo e(route('plans')); ?>" class="mine-text-secondary text-sm hover:text-(--mine-text-link) transition-colors duration-150">Plans</a>
                <a href="<?php echo e(route('categories')); ?>" class="mine-text-secondary text-sm hover:text-(--mine-text-link) transition-colors duration-150">Categories</a>
                <a href="<?php echo e(route('report-page')); ?>" class="mine-text-secondary text-sm hover:text-(--mine-text-link) transition-colors duration-150">Reports</a>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <h2 class="text-sm font-semibold mine-text-primary uppercase tracking-wider">Account</h2>
            <div class="flex flex-col gap-2">
                <a href="<?php echo e(route('profile')); ?>" class="mine-text-secondary text-sm hover:text-(--mine-text-link) transition-colors duration-150">Profile</a>
                <a href="#" class="mine-text-secondary text-sm hover:text-(--mine-text-link) transition-colors duration-150">Settings</a>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <h2 class="text-sm font-semibold mine-text-primary uppercase tracking-wider">Tech Stack</h2>
            <div class="flex flex-col gap-2">
                <span class="mine-text-secondary text-sm flex items-center gap-2">
                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'bolt','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bolt','class' => 'size-3.5']); ?>
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
<?php endif; ?> Laravel 13
                </span>
                <span class="mine-text-secondary text-sm flex items-center gap-2">
                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'sparkles','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'sparkles','class' => 'size-3.5']); ?>
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
<?php endif; ?> Livewire 4
                </span>
                <span class="mine-text-secondary text-sm flex items-center gap-2">
                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'swatch','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'swatch','class' => 'size-3.5']); ?>
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
<?php endif; ?> Tailwind CSS v4
                </span>
            </div>
        </div>
    </div>

    <div class="border-t border-(--mine-separator-border) px-8 py-4 flex flex-col sm:flex-row justify-between items-center gap-2">
        <p class="mine-text-secondary text-xs">&copy; <?php echo e(date('Y')); ?> Planner. All rights reserved.</p>
        <p class="mine-text-secondary text-xs">Made with care.</p>
    </div>
</div>
<?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\resources\views/components/mine/footer/index.blade.php ENDPATH**/ ?>