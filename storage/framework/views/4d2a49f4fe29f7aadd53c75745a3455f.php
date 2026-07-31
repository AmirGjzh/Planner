<div class="flex-1 px-4 sm:px-8 md:px-16 pb-6 pt-4 flex flex-col">
    <div class="mb-6">
        <h1 class="font-bold text-md mine-text-primary">My Profile</h1>
    </div>
    <div class="flex flex-col md:flex-row gap-6">
        <div class="mine-card flex-1 p-4 sm:p-6  flex flex-col sm:flex-row sm:items-center sm:justify-between md:flex-col md:items-center md:justify-center gap-4">
            <div class="flex items-center gap-4 md:flex-col">
                <div class="md:mt-20 size-20 shrink-0 rounded-full mine-badge-primary flex justify-center items-center">
                    <h1 class="font-bold text-xl"><?php echo e(strtoupper($this->user->firstname && $this->user->lastname ? substr($this->user->firstname, 0, 1) . substr($this->user->lastname, 0, 1) : substr($this->user->username, 0, 2))); ?></h1>
                </div>
                <div class="md:text-center md:flex-0 sm:flex-1 md:my-2 min-w-0">
                    <h1 class="font-bold text-md mine-text-primary mb-1 truncate"><?php echo e($this->user->username); ?></h1>
                    <p class="font-medium text-sm mine-text-secondary truncate"><?php echo e($this->user->email); ?></p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 md:w-full md:px-4 md:mt-auto">
                <?php if (isset($component)) { $__componentOriginal8d7c6f32b4231a85a51f9592ef197b78 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d7c6f32b4231a85a51f9592ef197b78 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.modal.trigger','data' => ['class' => 'w-full','id' => 'edit-profile-form']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.modal.trigger'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','id' => 'edit-profile-form']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php if (isset($component)) { $__componentOriginal3e1589af555879850f41f4137dd88178 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e1589af555879850f41f4137dd88178 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.button.index','data' => ['type' => 'button','class' => ' mine-btn-outline-primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','class' => ' mine-btn-outline-primary']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Edit Profile
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e1589af555879850f41f4137dd88178)): ?>
<?php $attributes = $__attributesOriginal3e1589af555879850f41f4137dd88178; ?>
<?php unset($__attributesOriginal3e1589af555879850f41f4137dd88178); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e1589af555879850f41f4137dd88178)): ?>
<?php $component = $__componentOriginal3e1589af555879850f41f4137dd88178; ?>
<?php unset($__componentOriginal3e1589af555879850f41f4137dd88178); ?>
<?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d7c6f32b4231a85a51f9592ef197b78)): ?>
<?php $attributes = $__attributesOriginal8d7c6f32b4231a85a51f9592ef197b78; ?>
<?php unset($__attributesOriginal8d7c6f32b4231a85a51f9592ef197b78); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d7c6f32b4231a85a51f9592ef197b78)): ?>
<?php $component = $__componentOriginal8d7c6f32b4231a85a51f9592ef197b78; ?>
<?php unset($__componentOriginal8d7c6f32b4231a85a51f9592ef197b78); ?>
<?php endif; ?>
            </div>
        </div>

        <div class="md:flex-2 flex flex-col">
            <div class="mine-card p-6 sm:p-8 flex flex-col gap-4">
                <div class="mb-4">
                    <h1 class="font-bold text-sm mine-text-primary">Personal Information</h1>
                </div>
                <div class="flex justify-between px-2">
                    <p class="text-sm font-medium mine-text-secondary">Firstname</p>
                    <p class="text-sm font-medium mine-text-secondary max-w-40 min-w-0 truncate">
                        <?php echo e($this->user->firstname ?? '—'); ?>

                    </p>
                </div>
                <div class="flex justify-between px-2">
                    <p class="text-sm font-medium mine-text-secondary">Lastname</p>
                    <p class="text-sm font-medium mine-text-secondary max-w-40 min-w-0 truncate">
                        <?php echo e($this->user->lastname ?? '—'); ?>

                    </p>
                </div>
                <div class="flex justify-between px-2">
                    <p class="text-sm font-medium mine-text-secondary">Birthday</p>
                    <p class="text-sm font-medium mine-text-secondary max-w-40 min-w-0 truncate">
                        <?php echo e($this->user->birthday?->format('Y-m-d') ?? '—'); ?>

                    </p>
                </div>
                <div class="flex justify-between px-2">
                    <p class="text-sm font-medium mine-text-secondary">Gender</p>
                    <p class="text-sm font-medium mine-text-secondary max-w-40 min-w-0 truncate"><?php echo e($this->user->gender ? ucfirst($this->user->gender->value) : '—'); ?>

                    </p>
                </div>
                <div class="flex justify-between px-2">
                    <p class="text-sm font-medium mine-text-secondary">Country</p>
                    <p class="text-sm font-medium mine-text-secondary max-w-40 min-w-0 truncate"><?php echo e($this->countries[$this->user->country] ?? '—'); ?>

                    </p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 px-4 sm:px-6 py-4 mt-6 sm:justify-between mine-alert-danger-box">
                <div class="flex items-center justify-center gap-4">
                    <div class="size-12 rounded-full mine-badge-danger flex justify-center items-center">
                        <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'trash','class' => 'size-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'trash','class' => 'size-6']); ?>
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
                    <p class="text-sm font-medium mine-text-secondary">Delete your account and all of your
                        data.</p>
                </div>
                <div class="flex justify-center items-center">
                    <?php if (isset($component)) { $__componentOriginal8d7c6f32b4231a85a51f9592ef197b78 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d7c6f32b4231a85a51f9592ef197b78 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.modal.trigger','data' => ['class' => 'w-full','id' => 'delete-account-confirmation']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.modal.trigger'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','id' => 'delete-account-confirmation']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php if (isset($component)) { $__componentOriginal3e1589af555879850f41f4137dd88178 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e1589af555879850f41f4137dd88178 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.button.index','data' => ['type' => 'button','class' => 'mine-btn-outline-danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','class' => 'mine-btn-outline-danger']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            Delete Account
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e1589af555879850f41f4137dd88178)): ?>
<?php $attributes = $__attributesOriginal3e1589af555879850f41f4137dd88178; ?>
<?php unset($__attributesOriginal3e1589af555879850f41f4137dd88178); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e1589af555879850f41f4137dd88178)): ?>
<?php $component = $__componentOriginal3e1589af555879850f41f4137dd88178; ?>
<?php unset($__componentOriginal3e1589af555879850f41f4137dd88178); ?>
<?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d7c6f32b4231a85a51f9592ef197b78)): ?>
<?php $attributes = $__attributesOriginal8d7c6f32b4231a85a51f9592ef197b78; ?>
<?php unset($__attributesOriginal8d7c6f32b4231a85a51f9592ef197b78); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d7c6f32b4231a85a51f9592ef197b78)): ?>
<?php $component = $__componentOriginal8d7c6f32b4231a85a51f9592ef197b78; ?>
<?php unset($__componentOriginal8d7c6f32b4231a85a51f9592ef197b78); ?>
<?php endif; ?>
                </div>
            </div>
        </div>
    </div>


    <?php if (isset($component)) { $__componentOriginald0a1bb337a23e26b240759e5d7b32a8d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0a1bb337a23e26b240759e5d7b32a8d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.modal.index','data' => ['id' => 'edit-profile-form','closeByClickingAway' => false,'closeByEscaping' => false,'width' => 'xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'edit-profile-form','close-by-clicking-away' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'close-by-escaping' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'width' => 'xl']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Edit your information</h2>
                <button type="button" @click="close(); $wire.cancelEdit()"
                        class="mine-btn-icon p-2 rounded-xl">
                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'x-mark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'x-mark']); ?>
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
                </button>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($edit_success === 'updated'): ?>
                <div class="mb-4">
                    <?php if (isset($component)) { $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.alert.index','data' => ['variant' => 'success','title' => 'Profile updated.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'success','title' => 'Profile updated.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Your profile has been updated
                        successfully.
                     <?php echo $__env->renderComponent(); ?>
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

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($edit_error === 'rate_limited'): ?>
                <div class="mb-4">
                    <?php if (isset($component)) { $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.alert.index','data' => ['variant' => 'warning','title' => 'Too many attempts.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'warning','title' => 'Too many attempts.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Please try again in a minute.
                     <?php echo $__env->renderComponent(); ?>
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

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($edit_error === 'username_taken'): ?>
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
That username is already in use.
                     <?php echo $__env->renderComponent(); ?>
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

            <form class="flex flex-col gap-4" wire:submit="editProfile">
                <div>
                    <?php if (isset($component)) { $__componentOriginal3b0c74b0bec77c654b4b768c72b88641 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0c74b0bec77c654b4b768c72b88641 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.input.index','data' => ['label' => 'Username','wire:model' => 'username']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Username','wire:model' => 'username']); ?>
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
                <div class="flex flex-col sm:flex-row gap-4">
                    <?php if (isset($component)) { $__componentOriginal3b0c74b0bec77c654b4b768c72b88641 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0c74b0bec77c654b4b768c72b88641 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.input.index','data' => ['label' => 'Firstname','wire:model' => 'firstname','placeholder' => 'Your Firstname']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Firstname','wire:model' => 'firstname','placeholder' => 'Your Firstname']); ?>
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
                    <?php if (isset($component)) { $__componentOriginal3b0c74b0bec77c654b4b768c72b88641 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0c74b0bec77c654b4b768c72b88641 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.input.index','data' => ['label' => 'Lastname','wire:model' => 'lastname','placeholder' => 'Your lastname']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Lastname','wire:model' => 'lastname','placeholder' => 'Your lastname']); ?>
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
                <div class="flex flex-col sm:flex-row gap-4">
                    <?php if (isset($component)) { $__componentOriginal9899aade606064e966c5bc9e549ee2a4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9899aade606064e966c5bc9e549ee2a4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.select.index','data' => ['wire:model' => 'gender','label' => 'Gender','placeholder' => 'Select gender']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'gender','label' => 'Gender','placeholder' => 'Select gender']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php if (isset($component)) { $__componentOriginald7bb1175d0d36542846e90497cea98e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald7bb1175d0d36542846e90497cea98e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.select.option','data' => ['value' => 'male']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.select.option'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 'male']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Male <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald7bb1175d0d36542846e90497cea98e9)): ?>
<?php $attributes = $__attributesOriginald7bb1175d0d36542846e90497cea98e9; ?>
<?php unset($__attributesOriginald7bb1175d0d36542846e90497cea98e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald7bb1175d0d36542846e90497cea98e9)): ?>
<?php $component = $__componentOriginald7bb1175d0d36542846e90497cea98e9; ?>
<?php unset($__componentOriginald7bb1175d0d36542846e90497cea98e9); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginald7bb1175d0d36542846e90497cea98e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald7bb1175d0d36542846e90497cea98e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.select.option','data' => ['value' => 'female']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.select.option'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 'female']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Female <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald7bb1175d0d36542846e90497cea98e9)): ?>
<?php $attributes = $__attributesOriginald7bb1175d0d36542846e90497cea98e9; ?>
<?php unset($__attributesOriginald7bb1175d0d36542846e90497cea98e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald7bb1175d0d36542846e90497cea98e9)): ?>
<?php $component = $__componentOriginald7bb1175d0d36542846e90497cea98e9; ?>
<?php unset($__componentOriginald7bb1175d0d36542846e90497cea98e9); ?>
<?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9899aade606064e966c5bc9e549ee2a4)): ?>
<?php $attributes = $__attributesOriginal9899aade606064e966c5bc9e549ee2a4; ?>
<?php unset($__attributesOriginal9899aade606064e966c5bc9e549ee2a4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9899aade606064e966c5bc9e549ee2a4)): ?>
<?php $component = $__componentOriginal9899aade606064e966c5bc9e549ee2a4; ?>
<?php unset($__componentOriginal9899aade606064e966c5bc9e549ee2a4); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginal9899aade606064e966c5bc9e549ee2a4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9899aade606064e966c5bc9e549ee2a4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.select.index','data' => ['wire:model' => 'country','label' => 'Country','placeholder' => 'Select Country','searchable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'country','label' => 'Country','placeholder' => 'Select Country','searchable' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginald7bb1175d0d36542846e90497cea98e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald7bb1175d0d36542846e90497cea98e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.select.option','data' => ['wire:key' => ''.e($code).'','value' => ''.e($code).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.select.option'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:key' => ''.e($code).'','value' => ''.e($code).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e($name); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald7bb1175d0d36542846e90497cea98e9)): ?>
<?php $attributes = $__attributesOriginald7bb1175d0d36542846e90497cea98e9; ?>
<?php unset($__attributesOriginald7bb1175d0d36542846e90497cea98e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald7bb1175d0d36542846e90497cea98e9)): ?>
<?php $component = $__componentOriginald7bb1175d0d36542846e90497cea98e9; ?>
<?php unset($__componentOriginald7bb1175d0d36542846e90497cea98e9); ?>
<?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9899aade606064e966c5bc9e549ee2a4)): ?>
<?php $attributes = $__attributesOriginal9899aade606064e966c5bc9e549ee2a4; ?>
<?php unset($__attributesOriginal9899aade606064e966c5bc9e549ee2a4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9899aade606064e966c5bc9e549ee2a4)): ?>
<?php $component = $__componentOriginal9899aade606064e966c5bc9e549ee2a4; ?>
<?php unset($__componentOriginal9899aade606064e966c5bc9e549ee2a4); ?>
<?php endif; ?>
                </div>
                <div>
                    <?php if (isset($component)) { $__componentOriginal4da2bb3c8aa4ea11b330eaaaf31f996c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4da2bb3c8aa4ea11b330eaaaf31f996c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.datepicker.index','data' => ['mode' => 'single','selectableMonths' => true,'selectableYears' => true,'position' => 'top-end','yearsRange' => [-50, 0],'wire:model' => 'birthday','label' => 'Birthday']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.datepicker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['mode' => 'single','selectable-months' => true,'selectable-years' => true,'position' => 'top-end','years-range' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([-50, 0]),'wire:model' => 'birthday','label' => 'Birthday']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4da2bb3c8aa4ea11b330eaaaf31f996c)): ?>
<?php $attributes = $__attributesOriginal4da2bb3c8aa4ea11b330eaaaf31f996c; ?>
<?php unset($__attributesOriginal4da2bb3c8aa4ea11b330eaaaf31f996c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4da2bb3c8aa4ea11b330eaaaf31f996c)): ?>
<?php $component = $__componentOriginal4da2bb3c8aa4ea11b330eaaaf31f996c; ?>
<?php unset($__componentOriginal4da2bb3c8aa4ea11b330eaaaf31f996c); ?>
<?php endif; ?>
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <?php if (isset($component)) { $__componentOriginal3e1589af555879850f41f4137dd88178 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e1589af555879850f41f4137dd88178 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.button.index','data' => ['type' => 'button','@click' => 'close(); $wire.cancelEdit()','class' => 'mine-btn-ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','@click' => 'close(); $wire.cancelEdit()','class' => 'mine-btn-ghost']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Cancel
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e1589af555879850f41f4137dd88178)): ?>
<?php $attributes = $__attributesOriginal3e1589af555879850f41f4137dd88178; ?>
<?php unset($__attributesOriginal3e1589af555879850f41f4137dd88178); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e1589af555879850f41f4137dd88178)): ?>
<?php $component = $__componentOriginal3e1589af555879850f41f4137dd88178; ?>
<?php unset($__componentOriginal3e1589af555879850f41f4137dd88178); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal3e1589af555879850f41f4137dd88178 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e1589af555879850f41f4137dd88178 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.button.index','data' => ['wire:target' => 'editProfile','class' => 'mine-btn-primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:target' => 'editProfile','class' => 'mine-btn-primary']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Save <?php echo $__env->renderComponent(); ?>
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
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0a1bb337a23e26b240759e5d7b32a8d)): ?>
<?php $attributes = $__attributesOriginald0a1bb337a23e26b240759e5d7b32a8d; ?>
<?php unset($__attributesOriginald0a1bb337a23e26b240759e5d7b32a8d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0a1bb337a23e26b240759e5d7b32a8d)): ?>
<?php $component = $__componentOriginald0a1bb337a23e26b240759e5d7b32a8d; ?>
<?php unset($__componentOriginald0a1bb337a23e26b240759e5d7b32a8d); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginald0a1bb337a23e26b240759e5d7b32a8d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0a1bb337a23e26b240759e5d7b32a8d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.modal.index','data' => ['id' => 'delete-account-confirmation','closeByClickingAway' => false,'closeByEscaping' => false,'width' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'delete-account-confirmation','close-by-clicking-away' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'close-by-escaping' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'width' => 'lg']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Delete your account</h2>
                <button type="button" @click="close(); $wire.cancelDelete()"
                        class="mine-btn-icon p-2 rounded-xl">
                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'x-mark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'x-mark']); ?>
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
                </button>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($delete_error === 'rate_limited'): ?>
                <?php if (isset($component)) { $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.alert.index','data' => ['variant' => 'warning','title' => 'Too many attempts.','class' => 'mb-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'warning','title' => 'Too many attempts.','class' => 'mb-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Please try again in a minute.
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3)): ?>
<?php $attributes = $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3; ?>
<?php unset($__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3)): ?>
<?php $component = $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3; ?>
<?php unset($__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($delete_error === 'wrong_password'): ?>
                <?php if (isset($component)) { $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.alert.index','data' => ['variant' => 'danger','title' => 'Wrong password.','class' => 'mb-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'danger','title' => 'Wrong password.','class' => 'mb-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
The password you entered is
                    incorrect.
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3)): ?>
<?php $attributes = $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3; ?>
<?php unset($__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3)): ?>
<?php $component = $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3; ?>
<?php unset($__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($delete_error)): ?>
                <?php if (isset($component)) { $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.alert.index','data' => ['variant' => 'warning','title' => 'Be Careful!','class' => 'mb-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'warning','title' => 'Be Careful!','class' => 'mb-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    This action is permanent and cannot be undone.<br>Enter your password to continue.
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3)): ?>
<?php $attributes = $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3; ?>
<?php unset($__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3)): ?>
<?php $component = $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3; ?>
<?php unset($__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <form wire:submit="deleteAccount" class="flex flex-col gap-6"
                @open-modal.window="if ($event.detail.id === 'delete-account-confirmation') { $nextTick(() => $el.querySelector('input')?.focus()) }">
                <?php if (isset($component)) { $__componentOriginal3b0c74b0bec77c654b4b768c72b88641 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0c74b0bec77c654b4b768c72b88641 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.input.index','data' => ['wire:model' => 'password','label' => 'Password','placeholder' => 'Enter your password','type' => 'password','leftIcon' => 'lock-closed']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'password','label' => 'Password','placeholder' => 'Enter your password','type' => 'password','leftIcon' => 'lock-closed']); ?>
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
                <div class="flex gap-4 justify-between items-center">
                    <?php if (isset($component)) { $__componentOriginal3e1589af555879850f41f4137dd88178 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e1589af555879850f41f4137dd88178 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.button.index','data' => ['type' => 'button','@click' => 'close(); $wire.cancelDelete()','class' => 'mine-btn-ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','@click' => 'close(); $wire.cancelDelete()','class' => 'mine-btn-ghost']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Cancel
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e1589af555879850f41f4137dd88178)): ?>
<?php $attributes = $__attributesOriginal3e1589af555879850f41f4137dd88178; ?>
<?php unset($__attributesOriginal3e1589af555879850f41f4137dd88178); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e1589af555879850f41f4137dd88178)): ?>
<?php $component = $__componentOriginal3e1589af555879850f41f4137dd88178; ?>
<?php unset($__componentOriginal3e1589af555879850f41f4137dd88178); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal3e1589af555879850f41f4137dd88178 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e1589af555879850f41f4137dd88178 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.button.index','data' => ['wire:target' => 'deleteAccount','class' => 'mine-btn-danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:target' => 'deleteAccount','class' => 'mine-btn-danger']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Delete Account
                     <?php echo $__env->renderComponent(); ?>
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
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0a1bb337a23e26b240759e5d7b32a8d)): ?>
<?php $attributes = $__attributesOriginald0a1bb337a23e26b240759e5d7b32a8d; ?>
<?php unset($__attributesOriginald0a1bb337a23e26b240759e5d7b32a8d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0a1bb337a23e26b240759e5d7b32a8d)): ?>
<?php $component = $__componentOriginald0a1bb337a23e26b240759e5d7b32a8d; ?>
<?php unset($__componentOriginald0a1bb337a23e26b240759e5d7b32a8d); ?>
<?php endif; ?>
</div><?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\storage\framework\views/livewire/views/d100b33f.blade.php ENDPATH**/ ?>