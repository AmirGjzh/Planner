<div class="min-h-dvh mine-page-bg px-6 py-4 flex justify-center items-center">
    <div class="mine-card w-full max-w-120 flex flex-col justify-center p-6">
        <h1 class="text-center font-medium text-xl mine-text-primary mb-2">Welcome back</h1>
        <p class="text-center text-sm mb-6 mine-text-secondary font-medium">Sign in to continue to your workspace</p>

        <form wire:submit="login" class="flex flex-col">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($login_error === 'rate_limited'): ?>
                <div class="mb-4">
                    <?php if (isset($component)) { $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.alert.index','data' => ['variant' => 'warning','title' => 'Too many sign in attempts.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'warning','title' => 'Too many sign in attempts.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Please try again in a minute. <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3)): ?>
<?php $attributes = $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3; ?>
<?php unset($__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3)): ?>
<?php $component = $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3; ?>
<?php unset($__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3); ?>
<?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($login_error === 'invalid'): ?>
                <div class="mb-4">
                    <?php if (isset($component)) { $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.alert.index','data' => ['variant' => 'danger','title' => 'Invalid credentials.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'danger','title' => 'Invalid credentials.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
The email or password you entered is incorrect. <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3)): ?>
<?php $attributes = $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3; ?>
<?php unset($__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3)): ?>
<?php $component = $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3; ?>
<?php unset($__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3); ?>
<?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="mb-4">
                <?php if (isset($component)) { $__componentOriginal3b0c74b0bec77c654b4b768c72b88641 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0c74b0bec77c654b4b768c72b88641 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.input.index','data' => ['wire:model' => 'email','label' => 'Email Address','placeholder' => 'Email Address','leftIcon' => 'envelope','height' => 'h-12']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'email','label' => 'Email Address','placeholder' => 'Email Address','leftIcon' => 'envelope','height' => 'h-12']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0c74b0bec77c654b4b768c72b88641)): ?>
<?php $attributes = $__attributesOriginal3b0c74b0bec77c654b4b768c72b88641; ?>
<?php unset($__attributesOriginal3b0c74b0bec77c654b4b768c72b88641); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0c74b0bec77c654b4b768c72b88641)): ?>
<?php $component = $__componentOriginal3b0c74b0bec77c654b4b768c72b88641; ?>
<?php unset($__componentOriginal3b0c74b0bec77c654b4b768c72b88641); ?>
<?php endif; ?>
            </div>

            <div class="mb-6">
                <?php if (isset($component)) { $__componentOriginal3b0c74b0bec77c654b4b768c72b88641 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0c74b0bec77c654b4b768c72b88641 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.input.index','data' => ['wire:model' => 'password','type' => 'password','label' => 'Password','placeholder' => 'Password','leftIcon' => 'lock-closed','height' => 'h-12']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'password','type' => 'password','label' => 'Password','placeholder' => 'Password','leftIcon' => 'lock-closed','height' => 'h-12']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0c74b0bec77c654b4b768c72b88641)): ?>
<?php $attributes = $__attributesOriginal3b0c74b0bec77c654b4b768c72b88641; ?>
<?php unset($__attributesOriginal3b0c74b0bec77c654b4b768c72b88641); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0c74b0bec77c654b4b768c72b88641)): ?>
<?php $component = $__componentOriginal3b0c74b0bec77c654b4b768c72b88641; ?>
<?php unset($__componentOriginal3b0c74b0bec77c654b4b768c72b88641); ?>
<?php endif; ?>
            </div>

            <div class="flex justify-between mb-6">
                <?php if (isset($component)) { $__componentOriginal6c127703e34d6ba2eb19721a26267718 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6c127703e34d6ba2eb19721a26267718 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.checkbox.index','data' => ['wire:model' => 'remember']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'remember']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Remember me <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6c127703e34d6ba2eb19721a26267718)): ?>
<?php $attributes = $__attributesOriginal6c127703e34d6ba2eb19721a26267718; ?>
<?php unset($__attributesOriginal6c127703e34d6ba2eb19721a26267718); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6c127703e34d6ba2eb19721a26267718)): ?>
<?php $component = $__componentOriginal6c127703e34d6ba2eb19721a26267718; ?>
<?php unset($__componentOriginal6c127703e34d6ba2eb19721a26267718); ?>
<?php endif; ?>
                <a href="#" class="text-sm mine-text-link font-medium">Forgot password?</a>
            </div>

            <div class="mb-4">
                <?php if (isset($component)) { $__componentOriginal3e1589af555879850f41f4137dd88178 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e1589af555879850f41f4137dd88178 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.button.index','data' => ['type' => 'submit','height' => 'h-12']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','height' => 'h-12']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Sign in <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e1589af555879850f41f4137dd88178)): ?>
<?php $attributes = $__attributesOriginal3e1589af555879850f41f4137dd88178; ?>
<?php unset($__attributesOriginal3e1589af555879850f41f4137dd88178); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e1589af555879850f41f4137dd88178)): ?>
<?php $component = $__componentOriginal3e1589af555879850f41f4137dd88178; ?>
<?php unset($__componentOriginal3e1589af555879850f41f4137dd88178); ?>
<?php endif; ?>
            </div>
        </form>

        <div class="mb-2 px-6">
            <?php if (isset($component)) { $__componentOriginala8d2726167e5b40331507ad086cad2d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8d2726167e5b40331507ad086cad2d8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.separator.index','data' => ['label' => 'OR']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.separator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'OR']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8d2726167e5b40331507ad086cad2d8)): ?>
<?php $attributes = $__attributesOriginala8d2726167e5b40331507ad086cad2d8; ?>
<?php unset($__attributesOriginala8d2726167e5b40331507ad086cad2d8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8d2726167e5b40331507ad086cad2d8)): ?>
<?php $component = $__componentOriginala8d2726167e5b40331507ad086cad2d8; ?>
<?php unset($__componentOriginala8d2726167e5b40331507ad086cad2d8); ?>
<?php endif; ?>
        </div>

        <div class="flex justify-center">
            <p class="text-sm mine-text-primary font-medium mr-2">Don't have an account?</p>
            <a wire:navigate.hover href="<?php echo e(route('register')); ?>" class="text-sm mine-text-link font-medium">Create account</a>
        </div>
    </div>
</div><?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\storage\framework\views/livewire/views/37a265ef.blade.php ENDPATH**/ ?>