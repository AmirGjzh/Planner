<div class="flex-1 px-4 sm:px-8 md:px-16 pb-6 pt-4 flex flex-col">
    <div class="mb-6">
        <h1 class="font-bold text-md mine-text-primary">My Categories</h1>
    </div>
    <div class="flex items-center justify-between gap-2 sm:gap-4 mb-6">
        <div class="w-full">
            <?php if (isset($component)) { $__componentOriginal3b0c74b0bec77c654b4b768c72b88641 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0c74b0bec77c654b4b768c72b88641 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.input.index','data' => ['wire:model.live.debounce.200ms' => 'search','placeholder' => 'Search categories...','leftIcon' => 'magnifying-glass']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live.debounce.200ms' => 'search','placeholder' => 'Search categories...','leftIcon' => 'magnifying-glass']); ?>
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
        <?php if (isset($component)) { $__componentOriginal8d9d9a2433755cf7d56f035a9a209ec7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d9d9a2433755cf7d56f035a9a209ec7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.dropdown.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php if (isset($component)) { $__componentOriginal86d0d670af6b704e65326c857d937cd8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal86d0d670af6b704e65326c857d937cd8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.dropdown.trigger','data' => ['as' => 'div']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.dropdown.trigger'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['as' => 'div']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <div class="w-full mine-btn-outline-primary flex items-center h-11 px-4 sm:pl-3! rounded-xl cursor-pointer select-none">
                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'arrow-long-up','class' => 'inline -mr-1 -ml-2.5 sm:ml-0','variant' => 'micro']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-long-up','class' => 'inline -mr-1 -ml-2.5 sm:ml-0','variant' => 'micro']); ?>
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
                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'arrow-long-down','class' => 'inline -ml-1 -mr-2.5 sm:mr-0','variant' => 'micro']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-long-down','class' => 'inline -ml-1 -mr-2.5 sm:mr-0','variant' => 'micro']); ?>
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
                    <p class="text-sm font-medium hidden sm:inline sm:pl-2"><span class="hidden sm:inline md:hidden">Sort</span><span class="hidden md:inline">Sort By</span></p>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal86d0d670af6b704e65326c857d937cd8)): ?>
<?php $attributes = $__attributesOriginal86d0d670af6b704e65326c857d937cd8; ?>
<?php unset($__attributesOriginal86d0d670af6b704e65326c857d937cd8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal86d0d670af6b704e65326c857d937cd8)): ?>
<?php $component = $__componentOriginal86d0d670af6b704e65326c857d937cd8; ?>
<?php unset($__componentOriginal86d0d670af6b704e65326c857d937cd8); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal56296032c13441aa205d5be3967112d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal56296032c13441aa205d5be3967112d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.dropdown.content','data' => ['class' => 'mt-1!']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.dropdown.content'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mt-1!']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php if (isset($component)) { $__componentOriginal135b68fa1b28eebe8a4d5c65205bf07d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal135b68fa1b28eebe8a4d5c65205bf07d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.dropdown.item','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.dropdown.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <div class="w-full py-2 px-4 flex items-center gap-2 hover:cursor-pointer"
                        @click="$wire.set('sort', 'latest')">
                        <p class="text-sm font-medium flex-1 <?php echo e($sort === 'latest' ? 'mine-text-link' : 'mine-text-secondary'); ?>">Latest</p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sort === 'latest'): ?>
                            <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['variant' => 'micro','name' => 'check','class' => 'size-4 mine-text-link']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'micro','name' => 'check','class' => 'size-4 mine-text-link']); ?>
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
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal135b68fa1b28eebe8a4d5c65205bf07d)): ?>
<?php $attributes = $__attributesOriginal135b68fa1b28eebe8a4d5c65205bf07d; ?>
<?php unset($__attributesOriginal135b68fa1b28eebe8a4d5c65205bf07d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal135b68fa1b28eebe8a4d5c65205bf07d)): ?>
<?php $component = $__componentOriginal135b68fa1b28eebe8a4d5c65205bf07d; ?>
<?php unset($__componentOriginal135b68fa1b28eebe8a4d5c65205bf07d); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal135b68fa1b28eebe8a4d5c65205bf07d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal135b68fa1b28eebe8a4d5c65205bf07d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.dropdown.item','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.dropdown.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <div class="w-full py-2 px-4 flex items-center gap-2 hover:cursor-pointer"
                        @click="$wire.set('sort', 'name')">
                        <p class="text-sm font-medium flex-1 <?php echo e($sort === 'name' ? 'mine-text-link' : 'mine-text-secondary'); ?>">Name</p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sort === 'name'): ?>
                            <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['variant' => 'micro','name' => 'check','class' => 'size-4 mine-text-link']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'micro','name' => 'check','class' => 'size-4 mine-text-link']); ?>
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
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal135b68fa1b28eebe8a4d5c65205bf07d)): ?>
<?php $attributes = $__attributesOriginal135b68fa1b28eebe8a4d5c65205bf07d; ?>
<?php unset($__attributesOriginal135b68fa1b28eebe8a4d5c65205bf07d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal135b68fa1b28eebe8a4d5c65205bf07d)): ?>
<?php $component = $__componentOriginal135b68fa1b28eebe8a4d5c65205bf07d; ?>
<?php unset($__componentOriginal135b68fa1b28eebe8a4d5c65205bf07d); ?>
<?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal56296032c13441aa205d5be3967112d5)): ?>
<?php $attributes = $__attributesOriginal56296032c13441aa205d5be3967112d5; ?>
<?php unset($__attributesOriginal56296032c13441aa205d5be3967112d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal56296032c13441aa205d5be3967112d5)): ?>
<?php $component = $__componentOriginal56296032c13441aa205d5be3967112d5; ?>
<?php unset($__componentOriginal56296032c13441aa205d5be3967112d5); ?>
<?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d9d9a2433755cf7d56f035a9a209ec7)): ?>
<?php $attributes = $__attributesOriginal8d9d9a2433755cf7d56f035a9a209ec7; ?>
<?php unset($__attributesOriginal8d9d9a2433755cf7d56f035a9a209ec7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d9d9a2433755cf7d56f035a9a209ec7)): ?>
<?php $component = $__componentOriginal8d9d9a2433755cf7d56f035a9a209ec7; ?>
<?php unset($__componentOriginal8d9d9a2433755cf7d56f035a9a209ec7); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal8d7c6f32b4231a85a51f9592ef197b78 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d7c6f32b4231a85a51f9592ef197b78 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.modal.trigger','data' => ['id' => 'add-category-form']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.modal.trigger'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'add-category-form']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php if (isset($component)) { $__componentOriginal3e1589af555879850f41f4137dd88178 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e1589af555879850f41f4137dd88178 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.button.index','data' => ['type' => 'button','class' => 'mine-btn-outline-primary pl-3! pr-3! sm:pr-4!']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','class' => 'mine-btn-outline-primary pl-3! pr-3! sm:pr-4!']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <div class="flex justify-center items-center gap-2">
                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'plus','class' => 'inline','variant' => 'micro']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus','class' => 'inline','variant' => 'micro']); ?>
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
                    <p class="text-sm font-medium hidden sm:inline"><span class="hidden sm:inline md:hidden">New</span><span class="hidden md:inline">New Category</span></p>
                </div>
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
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'category-'.e($category->id).''; ?>wire:key="category-<?php echo e($category->id); ?>" class="mine-card-interactive border-l-8
                        border-l-(--mine-category-border-left-green)
                        hover:border-l-(--mine-category-border-left-green-hover)
                        w-full flex flex-col justify-between gap-5 px-4 py-4
                        ">
                <div class="flex justify-between items-start">
                    <div class="flex gap-3 min-w-0">
                        <div class="rounded-xl size-14 shrink-0 mine-badge-primary flex justify-center items-center">
                            <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'folder','class' => 'size-7']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'folder','class' => 'size-7']); ?>
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
                        <div class="flex flex-col justify-between py-0.5 min-w-0">
                            <h2 class="mine-text-primary font-medium text-[15px] truncate"><?php echo e($category->name); ?></h2>
                            <p class="mine-text-secondary text-[13px] font-medium"><?php echo e($category->tasks_count ?: 'No'); ?> <?php echo e(Str::plural('Task', $category->tasks_count)); ?></p>
                        </div>
                    </div>
                    <?php if (isset($component)) { $__componentOriginal8d9d9a2433755cf7d56f035a9a209ec7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d9d9a2433755cf7d56f035a9a209ec7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.dropdown.index','data' => ['group' => 'category-actions']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['group' => 'category-actions']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php if (isset($component)) { $__componentOriginal86d0d670af6b704e65326c857d937cd8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal86d0d670af6b704e65326c857d937cd8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.dropdown.trigger','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.dropdown.trigger'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <div class="mine-btn-icon p-2 rounded-xl">
                                <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'ellipsis-horizontal','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'ellipsis-horizontal','class' => 'size-5']); ?>
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
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal86d0d670af6b704e65326c857d937cd8)): ?>
<?php $attributes = $__attributesOriginal86d0d670af6b704e65326c857d937cd8; ?>
<?php unset($__attributesOriginal86d0d670af6b704e65326c857d937cd8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal86d0d670af6b704e65326c857d937cd8)): ?>
<?php $component = $__componentOriginal86d0d670af6b704e65326c857d937cd8; ?>
<?php unset($__componentOriginal86d0d670af6b704e65326c857d937cd8); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal56296032c13441aa205d5be3967112d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal56296032c13441aa205d5be3967112d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.dropdown.content','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.dropdown.content'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php if (isset($component)) { $__componentOriginal135b68fa1b28eebe8a4d5c65205bf07d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal135b68fa1b28eebe8a4d5c65205bf07d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.dropdown.item','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.dropdown.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <div class="w-full py-2 px-4 flex items-center gap-2 mine-text-link hover:cursor-pointer"
                                    @click.stop="$wire.set('editing_id', <?php echo e($category->id); ?>, false);
                                        $wire.set('edit_name', <?php echo \Illuminate\Support\Js::from($category->name)->toHtml() ?>, false);
                                        $dispatch('open-modal', { id: 'edit-category-form' })">
                                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'pencil','class' => 'size-4','variant' => 'solid']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'pencil','class' => 'size-4','variant' => 'solid']); ?>
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
                                    <p class="text-sm font-medium">Edit</p>
                                </div>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal135b68fa1b28eebe8a4d5c65205bf07d)): ?>
<?php $attributes = $__attributesOriginal135b68fa1b28eebe8a4d5c65205bf07d; ?>
<?php unset($__attributesOriginal135b68fa1b28eebe8a4d5c65205bf07d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal135b68fa1b28eebe8a4d5c65205bf07d)): ?>
<?php $component = $__componentOriginal135b68fa1b28eebe8a4d5c65205bf07d; ?>
<?php unset($__componentOriginal135b68fa1b28eebe8a4d5c65205bf07d); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginal135b68fa1b28eebe8a4d5c65205bf07d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal135b68fa1b28eebe8a4d5c65205bf07d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.dropdown.item','data' => ['destructive' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.dropdown.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['destructive' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <div class="w-full py-2 px-4 flex items-center gap-2 mine-text-error hover:cursor-pointer"
                                    @click.stop="$wire.set('deleting_id', <?php echo e($category->id); ?>, false);
                                        $dispatch('open-modal', { id: 'delete-category-confirmation' })">
                                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'trash','class' => 'size-4','variant' => 'solid']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'trash','class' => 'size-4','variant' => 'solid']); ?>
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
                                    <p class="text-sm font-medium">Delete</p>
                                </div>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal135b68fa1b28eebe8a4d5c65205bf07d)): ?>
<?php $attributes = $__attributesOriginal135b68fa1b28eebe8a4d5c65205bf07d; ?>
<?php unset($__attributesOriginal135b68fa1b28eebe8a4d5c65205bf07d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal135b68fa1b28eebe8a4d5c65205bf07d)): ?>
<?php $component = $__componentOriginal135b68fa1b28eebe8a4d5c65205bf07d; ?>
<?php unset($__componentOriginal135b68fa1b28eebe8a4d5c65205bf07d); ?>
<?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal56296032c13441aa205d5be3967112d5)): ?>
<?php $attributes = $__attributesOriginal56296032c13441aa205d5be3967112d5; ?>
<?php unset($__attributesOriginal56296032c13441aa205d5be3967112d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal56296032c13441aa205d5be3967112d5)): ?>
<?php $component = $__componentOriginal56296032c13441aa205d5be3967112d5; ?>
<?php unset($__componentOriginal56296032c13441aa205d5be3967112d5); ?>
<?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d9d9a2433755cf7d56f035a9a209ec7)): ?>
<?php $attributes = $__attributesOriginal8d9d9a2433755cf7d56f035a9a209ec7; ?>
<?php unset($__attributesOriginal8d9d9a2433755cf7d56f035a9a209ec7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d9d9a2433755cf7d56f035a9a209ec7)): ?>
<?php $component = $__componentOriginal8d9d9a2433755cf7d56f035a9a209ec7; ?>
<?php unset($__componentOriginal8d9d9a2433755cf7d56f035a9a209ec7); ?>
<?php endif; ?>

                </div>
                <div class="flex justify-center items-center">
                    <?php if (isset($component)) { $__componentOriginal3e1589af555879850f41f4137dd88178 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e1589af555879850f41f4137dd88178 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.button.index','data' => ['type' => 'button','wire:target' => '','class' => 'mine-btn-outline-primary h-10!']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','wire:target' => '','class' => 'mine-btn-outline-primary h-10!']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <div class="flex items-center gap-2">
                            <p class="text-sm font-medium">View Tasks</p>
                            <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'arrow-long-right','variant' => 'micro','class' => 'size-4 mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-long-right','variant' => 'micro','class' => 'size-4 mt-1']); ?>
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
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->search): ?>
                <div class="col-span-full text-center py-12">
                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'magnifying-glass','class' => 'size-12 mx-auto mb-3 mine-text-secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'magnifying-glass','class' => 'size-12 mx-auto mb-3 mine-text-secondary']); ?>
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
                    <p class="mine-text-secondary text-sm font-medium">No categories found.</p>
                </div>
            <?php else: ?>
                <div class="col-span-full text-center py-12">
                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'folder-open','class' => 'size-12 mx-auto mb-3 mine-text-secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'folder-open','class' => 'size-12 mx-auto mb-3 mine-text-secondary']); ?>
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
                    <p class="mine-text-secondary text-sm font-medium">No categories yet.</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="mt-6 w-full">
        <?php echo e($this->categories->links(data: ['scrollTo' => false])); ?>

    </div>

    <?php if (isset($component)) { $__componentOriginald0a1bb337a23e26b240759e5d7b32a8d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0a1bb337a23e26b240759e5d7b32a8d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.modal.index','data' => ['id' => 'add-category-form','closeByClickingAway' => false,'closeByEscaping' => false,'width' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'add-category-form','close-by-clicking-away' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'close-by-escaping' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'width' => 'lg']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Add new category</h2>
                <button type="button" @click="close(); $wire.cancelAdd()" class="mine-btn-icon p-2 rounded-xl">
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

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($add_success === 'created'): ?>
                <div class="mb-4">
                    <?php if (isset($component)) { $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.alert.index','data' => ['variant' => 'success','title' => 'Category created.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'success','title' => 'Category created.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Your new category has been
                        added. <?php echo $__env->renderComponent(); ?>
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

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($add_error === 'already_exists'): ?>
                <div class="mb-4">
                    <?php if (isset($component)) { $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.alert.index','data' => ['variant' => 'danger','title' => 'Category already exists.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'danger','title' => 'Category already exists.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
A category with this name already
                        exists. <?php echo $__env->renderComponent(); ?>
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

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($add_error === 'rate_limited'): ?>
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

            <form class="flex flex-col gap-4" wire:submit="addCategory"
                @open-modal.window="if ($event.detail.id === 'add-category-form') { $nextTick(() => $refs.addCategoryInput?.focus()) }">
                <div>
                    <?php if (isset($component)) { $__componentOriginal3b0c74b0bec77c654b4b768c72b88641 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0c74b0bec77c654b4b768c72b88641 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.input.index','data' => ['label' => 'Category Name','wire:model' => 'add_category','placeholder' => 'Enter category name','xRef' => 'addCategoryInput']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Category Name','wire:model' => 'add_category','placeholder' => 'Enter category name','x-ref' => 'addCategoryInput']); ?>
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
                <div class="flex gap-4 justify-end mt-4">
                    <?php if (isset($component)) { $__componentOriginal3e1589af555879850f41f4137dd88178 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e1589af555879850f41f4137dd88178 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.button.index','data' => ['type' => 'button','@click' => 'close(); $wire.cancelAdd()','class' => 'mine-btn-ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','@click' => 'close(); $wire.cancelAdd()','class' => 'mine-btn-ghost']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.button.index','data' => ['wire:target' => 'addCategory','class' => 'mine-btn-primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:target' => 'addCategory','class' => 'mine-btn-primary']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Add <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.modal.index','data' => ['id' => 'edit-category-form','closeByClickingAway' => false,'closeByEscaping' => false,'width' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'edit-category-form','close-by-clicking-away' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'close-by-escaping' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'width' => 'lg']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Edit category</h2>
                <button type="button" @click="close(); $wire.cancelEdit()" class="mine-btn-icon p-2 rounded-xl">
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.alert.index','data' => ['variant' => 'success','title' => 'Category updated.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'success','title' => 'Category updated.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Your category has been updated. <?php echo $__env->renderComponent(); ?>
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

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($edit_error === 'already_exists'): ?>
                <div class="mb-4">
                    <?php if (isset($component)) { $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.alert.index','data' => ['variant' => 'danger','title' => 'Category already exists.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'danger','title' => 'Category already exists.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
A category with this name already
                        exists. <?php echo $__env->renderComponent(); ?>
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

            <form class="flex flex-col gap-4" wire:submit="editCategory"
                @open-modal.window="if ($event.detail.id === 'edit-category-form') { $nextTick(() => $refs.editCategoryInput?.focus()) }">
                <div>
                    <?php if (isset($component)) { $__componentOriginal3b0c74b0bec77c654b4b768c72b88641 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0c74b0bec77c654b4b768c72b88641 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.input.index','data' => ['label' => 'Category Name','wire:model' => 'edit_name','placeholder' => 'Enter category name','xRef' => 'editCategoryInput']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Category Name','wire:model' => 'edit_name','placeholder' => 'Enter category name','x-ref' => 'editCategoryInput']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.button.index','data' => ['wire:target' => 'editCategory','class' => 'mine-btn-primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:target' => 'editCategory','class' => 'mine-btn-primary']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.modal.index','data' => ['id' => 'delete-category-confirmation','closeByClickingAway' => false,'closeByEscaping' => false,'width' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'delete-category-confirmation','close-by-clicking-away' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'close-by-escaping' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'width' => 'lg']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Delete category</h2>
                <button type="button" @click="close(); $wire.cancelDelete()" class="mine-btn-icon p-2 rounded-xl">
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

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($delete_error === 'has_tasks'): ?>
                <div class="mb-4">
                    <?php if (isset($component)) { $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.alert.index','data' => ['variant' => 'danger','title' => 'Cannot delete category.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'danger','title' => 'Cannot delete category.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
This category has tasks. Reassign or
                        delete them first. <?php echo $__env->renderComponent(); ?>
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
            <?php else: ?>
                <div class="mb-4">
                    <?php if (isset($component)) { $__componentOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49c24fdfbb52fbdcbf30249ba83f4ae3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.alert.index','data' => ['variant' => 'warning','title' => 'Are you sure?']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'warning','title' => 'Are you sure?']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
This action cannot be undone. <?php echo $__env->renderComponent(); ?>
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

            <form class="flex flex-col gap-4" wire:submit="deleteCategory">
                <div class="flex gap-4 justify-end mt-4">
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.button.index','data' => ['wire:target' => 'deleteCategory','class' => 'mine-btn-danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:target' => 'deleteCategory','class' => 'mine-btn-danger']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Delete <?php echo $__env->renderComponent(); ?>
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
</div><?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\storage\framework\views/livewire/views/2fff3482.blade.php ENDPATH**/ ?>