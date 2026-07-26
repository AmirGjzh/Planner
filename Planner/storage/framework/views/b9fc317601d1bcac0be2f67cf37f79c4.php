<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => $attributes->whereStartsWith('wire:model')->first() ?? $attributes->whereStartsWith('x-model')->first(),
    'label' => null,
    'type' => 'text',
    'placeholder' => null,
    'helper' => null,
    'required' => false,
    'leftIcon' => null,
    'height' => 'h-11',
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
    'name' => $attributes->whereStartsWith('wire:model')->first() ?? $attributes->whereStartsWith('x-model')->first(),
    'label' => null,
    'type' => 'text',
    'placeholder' => null,
    'helper' => null,
    'required' => false,
    'leftIcon' => null,
    'height' => 'h-11',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<style>
    .mine-input input::-ms-reveal,
    .mine-input input::-ms-clear {
        display: none;
    }
    .mine-input input:-webkit-autofill,
    .mine-input input:-webkit-autofill:hover,
    .mine-input input:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0 1000px var(--mine-input-autofill-bg, var(--mine-input-bg)) inset !important;
        -webkit-text-fill-color: var(--mine-text-primary) !important;
        caret-color: var(--mine-text-primary) !important;
    }
</style>

<div class="mine-input w-full">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($label): ?>
        <?php if (isset($component)) { $__componentOriginal3711d9369843ae1d6cfa5c90065dd636 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3711d9369843ae1d6cfa5c90065dd636 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.input.label','data' => ['for' => $name,'required' => $required]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.input.label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($required)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php echo e($label); ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3711d9369843ae1d6cfa5c90065dd636)): ?>
<?php $attributes = $__attributesOriginal3711d9369843ae1d6cfa5c90065dd636; ?>
<?php unset($__attributesOriginal3711d9369843ae1d6cfa5c90065dd636); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3711d9369843ae1d6cfa5c90065dd636)): ?>
<?php $component = $__componentOriginal3711d9369843ae1d6cfa5c90065dd636; ?>
<?php unset($__componentOriginal3711d9369843ae1d6cfa5c90065dd636); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div
        <?php if($type === 'password'): ?> x-data="{ show: false }" <?php endif; ?>
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'group flex w-full items-center overflow-hidden rounded-xl border-2 transition-all duration-200 ease-out',
            $height => true,
            'bg-[var(--mine-input-bg)]' => !$errors->has($name),
            'bg-[var(--mine-input-error-bg)]' => $errors->has($name),
            'border-[var(--mine-input-border)]' => !$errors->has($name),
            'border-[var(--mine-input-error-border)]' => $errors->has($name),
            'focus-within:border-[var(--mine-input-border-focus)] focus-within:ring-4 focus-within:ring-[var(--mine-input-ring-focus)]' => !$errors->has($name),
            'focus-within:border-[var(--mine-input-error-border)] focus-within:ring-[var(--mine-input-error-ring)] focus-within:ring-4' => $errors->has($name)
        ]); ?>"
        <?php if($errors->has($name)): ?> style="--mine-input-autofill-bg: var(--mine-input-error-bg); --mine-input-placeholder: var(--mine-input-error-placeholder)" <?php endif; ?>
    >
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($leftIcon): ?>
            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'flex h-full items-center pl-4',
                'text-(--mine-input-icon)' => !$errors->has($name),
                'text-(--mine-input-error-icon)' => $errors->has($name),
                'group-focus-within:text-(--mine-input-border-focus)' => !$errors->has($name),
                'group-focus-within:text-(--mine-input-error-border)' => $errors->has($name),
            ]); ?>">
                <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['variant' => 'solid','name' => $leftIcon]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'solid','name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($leftIcon)]); ?>
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
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <input
            id="<?php echo e($name); ?>"
            name="<?php echo e($name); ?>"
            <?php if($type === 'password'): ?> :type="show ? 'text' : 'password'" <?php else: ?> type="<?php echo e($type); ?>" <?php endif; ?>
            placeholder="<?php echo e($placeholder); ?>"
            <?php echo e($attributes->except('class')->merge([
                'class' => '
                    h-full
                    w-full
                    bg-transparent
                    px-4
                    text-[15px]
                    mine-text-primary
                    placeholder:text-[var(--mine-input-placeholder)]
                    outline-none
                '
            ])); ?>

        >

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'password'): ?>
            <button
                type="button"
                @click="show = !show"
                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    'flex h-full items-center pr-4 hover:cursor-pointer transition-colors duration-200 focus-visible:outline-none',
                    'text-(--mine-input-icon)' => !$errors->has($name),
                    'text-(--mine-input-error-icon)' => $errors->has($name),
                    'group-focus-within:text-(--mine-input-border-focus)' => !$errors->has($name),
                    'group-focus-within:text-(--mine-input-error-border)' => $errors->has($name),
                    'focus-visible:ring-4 focus-visible:ring-(--mine-input-ring-focus)' => !$errors->has($name),
                    'focus-visible:ring-4 focus-visible:ring-(--mine-input-error-ring)' => $errors->has($name),
                ]); ?>"
            >
                <template x-if="!show">
                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['variant' => 'solid','name' => 'eye-slash']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'solid','name' => 'eye-slash']); ?>
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
                </template>
                <template x-if="show">
                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['variant' => 'solid','name' => 'eye']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'solid','name' => 'eye']); ?>
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
                </template>
            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if (isset($component)) { $__componentOriginal483bc289be8f83f3b6ec347e35026143 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal483bc289be8f83f3b6ec347e35026143 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.input.error','data' => ['name' => $name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.input.error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal483bc289be8f83f3b6ec347e35026143)): ?>
<?php $attributes = $__attributesOriginal483bc289be8f83f3b6ec347e35026143; ?>
<?php unset($__attributesOriginal483bc289be8f83f3b6ec347e35026143); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal483bc289be8f83f3b6ec347e35026143)): ?>
<?php $component = $__componentOriginal483bc289be8f83f3b6ec347e35026143; ?>
<?php unset($__componentOriginal483bc289be8f83f3b6ec347e35026143); ?>
<?php endif; ?>
</div>
<?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\resources\views/components/mine/input/index.blade.php ENDPATH**/ ?>