<div class="min-h-dvh mine-page-bg px-6 py-4 flex justify-center items-center">
    <div class="mine-card w-full max-w-120 flex flex-col justify-center p-6">
        <h1 class="text-center font-medium text-xl mine-text-primary mb-2">Create your account</h1>
        <p class="text-center text-sm mb-6 mine-text-secondary font-medium">Let's get you started</p>

        <form wire:submit="register" class="flex flex-col">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($register_error === 'rate_limited'): ?>
                <div class="mb-4">
                    <?php if (isset($component)) { $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.alert.index','data' => ['variant' => 'warning','title' => 'Too many sign up attempts.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'warning','title' => 'Too many sign up attempts.']); ?>
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
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($register_error === 'username_taken'): ?>
                <div class="mb-4">
                    <?php if (isset($component)) { $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.alert.index','data' => ['variant' => 'danger','title' => 'Username already taken.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'danger','title' => 'Username already taken.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
That username is already in use. <?php echo $__env->renderComponent(); ?>
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
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($register_error === 'email_taken'): ?>
                <div class="mb-4">
                    <?php if (isset($component)) { $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.alert.index','data' => ['variant' => 'danger','title' => 'Email already taken.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'danger','title' => 'Email already taken.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
That email is already registered. <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.input.index','data' => ['wire:model' => 'username','label' => 'Username','placeholder' => 'Username','leftIcon' => 'user','height' => 'h-12']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'username','label' => 'Username','placeholder' => 'Username','leftIcon' => 'user','height' => 'h-12']); ?>
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

            <div class="mb-4">
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

            <div class="mb-8">
                <?php if (isset($component)) { $__componentOriginal3b0c74b0bec77c654b4b768c72b88641 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0c74b0bec77c654b4b768c72b88641 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.input.index','data' => ['wire:model' => 'password_confirmation','type' => 'password','label' => 'Confirm Password','placeholder' => 'Confirm Password','leftIcon' => 'lock-closed','height' => 'h-12']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'password_confirmation','type' => 'password','label' => 'Confirm Password','placeholder' => 'Confirm Password','leftIcon' => 'lock-closed','height' => 'h-12']); ?>
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
Sign up <?php echo $__env->renderComponent(); ?>
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
            <p class="text-sm mine-text-primary font-medium mr-2">Already have an account?</p>
            <a wire:navigate.hover href="<?php echo e(route('login')); ?>" class="text-sm mine-text-link font-medium">Sign in</a>
        </div>
    </div>
</div><?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\storage\framework\views/livewire/views/26bb4bd3.blade.php ENDPATH**/ ?>