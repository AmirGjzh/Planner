<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo e($title ?? config('app.name')); ?></title>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scriptConfig(); ?>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>

<body class="min-h-dvh flex flex-col mine-page-bg">
    <?php if (isset($component)) { $__componentOriginal87e0a7fff90a6ad81b09a729e6b0c80e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal87e0a7fff90a6ad81b09a729e6b0c80e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.header.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal87e0a7fff90a6ad81b09a729e6b0c80e)): ?>
<?php $attributes = $__attributesOriginal87e0a7fff90a6ad81b09a729e6b0c80e; ?>
<?php unset($__attributesOriginal87e0a7fff90a6ad81b09a729e6b0c80e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal87e0a7fff90a6ad81b09a729e6b0c80e)): ?>
<?php $component = $__componentOriginal87e0a7fff90a6ad81b09a729e6b0c80e; ?>
<?php unset($__componentOriginal87e0a7fff90a6ad81b09a729e6b0c80e); ?>
<?php endif; ?>
    <?php echo e($slot); ?>

    <?php if (isset($component)) { $__componentOriginala526dd74a181c2c088234d66bd5d129a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala526dd74a181c2c088234d66bd5d129a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.footer.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala526dd74a181c2c088234d66bd5d129a)): ?>
<?php $attributes = $__attributesOriginala526dd74a181c2c088234d66bd5d129a; ?>
<?php unset($__attributesOriginala526dd74a181c2c088234d66bd5d129a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala526dd74a181c2c088234d66bd5d129a)): ?>
<?php $component = $__componentOriginala526dd74a181c2c088234d66bd5d129a; ?>
<?php unset($__componentOriginala526dd74a181c2c088234d66bd5d129a); ?>
<?php endif; ?>
</body>

</html>
<?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\resources\views/layouts/app.blade.php ENDPATH**/ ?>