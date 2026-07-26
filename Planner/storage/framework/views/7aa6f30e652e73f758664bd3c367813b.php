<?php $routeName = request()->route()?->getName(); ?>

<div class=" flex  h-16 px-8 m-2 mine-card justify-between items-center overflow-visible! z-40">
    <div class="hidden sm:flex items-center">
        
        <div class="pr-12">
            <div class="pr-3"></div>
            <h1 class="text-lg font-medium">Planner</h1>
        </div>
        
        <div class="flex gap-4 pr-4">
            <?php if (isset($component)) { $__componentOriginal09b0a11a508371f8082536a2dcefe893 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal09b0a11a508371f8082536a2dcefe893 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.nav-link.index','data' => ['href' => ''.e(route('dashboard')).'','route' => 'dashboard','active' => $routeName === 'dashboard']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('dashboard')).'','route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('dashboard'),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($routeName === 'dashboard')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Dashboard
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal09b0a11a508371f8082536a2dcefe893)): ?>
<?php $attributes = $__attributesOriginal09b0a11a508371f8082536a2dcefe893; ?>
<?php unset($__attributesOriginal09b0a11a508371f8082536a2dcefe893); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal09b0a11a508371f8082536a2dcefe893)): ?>
<?php $component = $__componentOriginal09b0a11a508371f8082536a2dcefe893; ?>
<?php unset($__componentOriginal09b0a11a508371f8082536a2dcefe893); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal09b0a11a508371f8082536a2dcefe893 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal09b0a11a508371f8082536a2dcefe893 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.nav-link.index','data' => ['href' => ''.e(route('task-page')).'','route' => 'task-page','active' => $routeName === 'task-page']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('task-page')).'','route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('task-page'),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($routeName === 'task-page')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Tasks
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal09b0a11a508371f8082536a2dcefe893)): ?>
<?php $attributes = $__attributesOriginal09b0a11a508371f8082536a2dcefe893; ?>
<?php unset($__attributesOriginal09b0a11a508371f8082536a2dcefe893); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal09b0a11a508371f8082536a2dcefe893)): ?>
<?php $component = $__componentOriginal09b0a11a508371f8082536a2dcefe893; ?>
<?php unset($__componentOriginal09b0a11a508371f8082536a2dcefe893); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal09b0a11a508371f8082536a2dcefe893 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal09b0a11a508371f8082536a2dcefe893 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.nav-link.index','data' => ['href' => ''.e(route('plans')).'','route' => 'plans','active' => $routeName === 'plans']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('plans')).'','route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('plans'),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($routeName === 'plans')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Plans
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal09b0a11a508371f8082536a2dcefe893)): ?>
<?php $attributes = $__attributesOriginal09b0a11a508371f8082536a2dcefe893; ?>
<?php unset($__attributesOriginal09b0a11a508371f8082536a2dcefe893); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal09b0a11a508371f8082536a2dcefe893)): ?>
<?php $component = $__componentOriginal09b0a11a508371f8082536a2dcefe893; ?>
<?php unset($__componentOriginal09b0a11a508371f8082536a2dcefe893); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal09b0a11a508371f8082536a2dcefe893 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal09b0a11a508371f8082536a2dcefe893 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.nav-link.index','data' => ['href' => ''.e(route('categories')).'','route' => 'category-page','active' => $routeName === 'category-page']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('categories')).'','route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('category-page'),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($routeName === 'category-page')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Categories
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal09b0a11a508371f8082536a2dcefe893)): ?>
<?php $attributes = $__attributesOriginal09b0a11a508371f8082536a2dcefe893; ?>
<?php unset($__attributesOriginal09b0a11a508371f8082536a2dcefe893); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal09b0a11a508371f8082536a2dcefe893)): ?>
<?php $component = $__componentOriginal09b0a11a508371f8082536a2dcefe893; ?>
<?php unset($__componentOriginal09b0a11a508371f8082536a2dcefe893); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal09b0a11a508371f8082536a2dcefe893 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal09b0a11a508371f8082536a2dcefe893 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.nav-link.index','data' => ['href' => ''.e(route('report-page')).'','route' => 'report-page','active' => $routeName === 'report-page']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('report-page')).'','route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('report-page'),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($routeName === 'report-page')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Reports
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal09b0a11a508371f8082536a2dcefe893)): ?>
<?php $attributes = $__attributesOriginal09b0a11a508371f8082536a2dcefe893; ?>
<?php unset($__attributesOriginal09b0a11a508371f8082536a2dcefe893); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal09b0a11a508371f8082536a2dcefe893)): ?>
<?php $component = $__componentOriginal09b0a11a508371f8082536a2dcefe893; ?>
<?php unset($__componentOriginal09b0a11a508371f8082536a2dcefe893); ?>
<?php endif; ?>
        </div>
    </div>
    <div class="hidden sm:flex items-center">
        <div class="pr-2">
            
        </div>
        <?php if (isset($component)) { $__componentOriginal8d9d9a2433755cf7d56f035a9a209ec7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d9d9a2433755cf7d56f035a9a209ec7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.dropdown.index','data' => ['triggerMode' => 'hover']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['trigger-mode' => 'hover']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php if (isset($component)) { $__componentOriginal86d0d670af6b704e65326c857d937cd8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal86d0d670af6b704e65326c857d937cd8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.dropdown.trigger','data' => ['class' => 'h-10 flex items-center rounded-xl p-2 hover:cursor-pointer text-[var(--mine-theme-switcher-color)] hover:bg-[var(--mine-theme-switcher-bg-hover)]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.dropdown.trigger'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-10 flex items-center rounded-xl p-2 hover:cursor-pointer text-[var(--mine-theme-switcher-color)] hover:bg-[var(--mine-theme-switcher-bg-hover)]']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'user']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'user']); ?>
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
                <div class="relative size-4">
                    <div
                        :class="!open ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
                        class="absolute inset-0 transition-all duration-200 ease-out"
                    >
                        <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'chevron-down','variant' => 'mini','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-down','variant' => 'mini','class' => 'size-4']); ?>
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
                    <div
                        :class="open ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
                        class="absolute inset-0 transition-all duration-200 ease-out"
                    >
                        <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'chevron-up','variant' => 'mini','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-up','variant' => 'mini','class' => 'size-4']); ?>
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

                <div class="flex items-center gap-4 rounded-xl p-2">
                    <div class="size-12 rounded-xl bg-[var(--mine-theme-switcher-bg-hover)] text-[var(--mine-theme-switcher-color)] flex justify-center items-center text-sm font-medium leading-none">
                        <?php echo e(strtoupper(substr(auth()->user()->username, 0, 2))); ?>

                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[var(--mine-text-primary)]">
                            <?php echo e(auth()->user()->username); ?>

                        </p>
                        <p class="text-xs font-medium text-[var(--mine-text-secondary)]">
                            <?php echo e(auth()->user()->email); ?>

                        </p>
                    </div>
                </div>

                <?php if (isset($component)) { $__componentOriginal3e31ef8fb56325d56331eaf46f2b93b1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e31ef8fb56325d56331eaf46f2b93b1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.dropdown.divider','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.dropdown.divider'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e31ef8fb56325d56331eaf46f2b93b1)): ?>
<?php $attributes = $__attributesOriginal3e31ef8fb56325d56331eaf46f2b93b1; ?>
<?php unset($__attributesOriginal3e31ef8fb56325d56331eaf46f2b93b1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e31ef8fb56325d56331eaf46f2b93b1)): ?>
<?php $component = $__componentOriginal3e31ef8fb56325d56331eaf46f2b93b1; ?>
<?php unset($__componentOriginal3e31ef8fb56325d56331eaf46f2b93b1); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal135b68fa1b28eebe8a4d5c65205bf07d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal135b68fa1b28eebe8a4d5c65205bf07d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.dropdown.item','data' => ['href' => ''.e(route('profile')).'','class' => 'mb-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.dropdown.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('profile')).'','class' => 'mb-2']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'identification','class' => 'mr-3 text-[var(--mine-text-primary)]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'identification','class' => 'mr-3 text-[var(--mine-text-primary)]']); ?>
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
                    <p class="text-sm text-[var(--mine-text-primary)]">Profile</p>
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

                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'cog-6-tooth','class' => 'mr-3 text-[var(--mine-text-primary)]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'cog-6-tooth','class' => 'mr-3 text-[var(--mine-text-primary)]']); ?>
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
                    <p class="text-sm text-[var(--mine-text-primary)]">Settings</p>
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

                <?php if (isset($component)) { $__componentOriginal3e31ef8fb56325d56331eaf46f2b93b1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e31ef8fb56325d56331eaf46f2b93b1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.dropdown.divider','data' => ['class' => 'px-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.dropdown.divider'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'px-2']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e31ef8fb56325d56331eaf46f2b93b1)): ?>
<?php $attributes = $__attributesOriginal3e31ef8fb56325d56331eaf46f2b93b1; ?>
<?php unset($__attributesOriginal3e31ef8fb56325d56331eaf46f2b93b1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e31ef8fb56325d56331eaf46f2b93b1)): ?>
<?php $component = $__componentOriginal3e31ef8fb56325d56331eaf46f2b93b1; ?>
<?php unset($__componentOriginal3e31ef8fb56325d56331eaf46f2b93b1); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal135b68fa1b28eebe8a4d5c65205bf07d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal135b68fa1b28eebe8a4d5c65205bf07d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.dropdown.item','data' => ['destructive' => true,'xData' => true,'xOn:click.prevent' => '
                    fetch(\''.e(route('logout')).'\', {
                        method: \'POST\',
                        headers: { \'X-CSRF-TOKEN\': \''.e(csrf_token()).'\' }
                    }).then(() => window.Livewire?.navigate(\''.e(route('login')).'\'))
                ']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.dropdown.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['destructive' => true,'x-data' => true,'x-on:click.prevent' => '
                    fetch(\''.e(route('logout')).'\', {
                        method: \'POST\',
                        headers: { \'X-CSRF-TOKEN\': \''.e(csrf_token()).'\' }
                    }).then(() => window.Livewire?.navigate(\''.e(route('login')).'\'))
                ']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'arrow-right-start-on-rectangle','class' => 'mr-3 text-[var(--mine-text-error)]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right-start-on-rectangle','class' => 'mr-3 text-[var(--mine-text-error)]']); ?>
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
                    <p class="text-sm font-medium text-[var(--mine-text-error)]">Logout</p>
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
</div>
<?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\resources\views/components/mine/header/index.blade.php ENDPATH**/ ?>