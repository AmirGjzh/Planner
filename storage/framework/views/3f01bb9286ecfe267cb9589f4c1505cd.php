<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'danger',
    'title' => null,
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
    'variant' => 'danger',
    'title' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $variants = [
        'danger' => [
            'container' => 'border-[var(--mine-alert-danger-border)] bg-[var(--mine-alert-danger-bg)]',
            'icon' => 'shield-exclamation',
            'icon-color' => 'text-[var(--mine-alert-danger-icon)]',
            'title' => 'text-[var(--mine-alert-danger-title)]',
        ],
        'success' => [
            'container' => 'border-[var(--mine-alert-success-border)] bg-[var(--mine-alert-success-bg)]',
            'icon' => 'check-circle',
            'icon-color' => 'text-[var(--mine-alert-success-icon)]',
            'title' => 'text-[var(--mine-alert-success-title)]',
        ],
        'warning' => [
            'container' => 'border-[var(--mine-alert-warning-border)] bg-[var(--mine-alert-warning-bg)]',
            'icon' => 'exclamation-triangle',
            'icon-color' => 'text-[var(--mine-alert-warning-icon)]',
            'title' => 'text-[var(--mine-alert-warning-title)]',
        ],
        'info' => [
            'container' => 'border-[var(--mine-alert-info-border)] bg-[var(--mine-alert-info-bg)]',
            'icon' => 'information-circle',
            'icon-color' => 'text-[var(--mine-alert-info-icon)]',
            'title' => 'text-[var(--mine-alert-info-title)]',
        ],
    ];

    $style = $variants[$variant];
?>

<div
    <?php echo e($attributes->class([
        'flex items-start gap-4 rounded-xl border-2 p-4',
        $style['container'],
    ])); ?>

>
    <div class="<?php echo e($style['icon-color']); ?> pt-0.5">
        <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => ''.e($style['icon']).'','class' => 'size-7']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($style['icon']).'','class' => 'size-7']); ?>
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
    <div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($title): ?>
            <h3 class="text-sm font-semibold <?php echo e($style['title']); ?>">
                <?php echo e($title); ?>

            </h3>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="mt-1 font-medium text-sm mine-text-secondary">
            <?php echo e($slot); ?>

        </div>
    </div>
</div>
<?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\resources\views/components/mine/alert/index.blade.php ENDPATH**/ ?>