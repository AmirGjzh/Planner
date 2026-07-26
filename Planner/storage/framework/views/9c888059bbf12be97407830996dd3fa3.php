<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => $attributes->whereStartsWith('wire:model')->first() ?? $attributes->whereStartsWith('x-model')->first(),
    'label' => null,
    'placeholder' => 'Select...',
    'searchable' => false,
    'disabled' => false,
    'invalid' => false,
    'position' => 'bottom-start',
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
    'placeholder' => 'Select...',
    'searchable' => false,
    'disabled' => false,
    'invalid' => false,
    'position' => 'bottom-start',
    'height' => 'h-11',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $positionClasses = str_starts_with($position, 'top')
        ? 'bottom-full mb-1.5 left-0 right-0'
        : 'top-full mt-1.5 left-0 right-0';

    if ($name && $errors->has($name)) {
        $invalid = true;
    }
?>

<div
    x-data="{
        state: null,
        selectedLabel: <?php echo \Illuminate\Support\Js::from($placeholder)->toHtml() ?>,
        isOpen: false,
        search: '',
        resultsCount: 0,

        init() {
            <?php if($name): ?>
                if (typeof $wire !== 'undefined') {
                    this.state = $wire.get(<?php echo \Illuminate\Support\Js::from($name)->toHtml() ?>)
                    $wire.$watch(<?php echo \Illuminate\Support\Js::from($name)->toHtml() ?>, (value) => {
                        this.state = value
                        this.syncLabel()
                    })
                }
            <?php endif; ?>

            if (this.hasSelection) {
                this.syncLabel()
            }

            this.$watch('state', () => this.syncLabel())

            this.$watch('search', () => {
                this.$nextTick(() => {
                    this.resultsCount = Array.from(this.$root.querySelectorAll('[role=option]'))
                        .filter(el => el.offsetParent !== null).length
                })
            })
        },

        syncLabel() {
            this.$nextTick(() => {
                if (this.hasSelection) {
                    const options = this.$root.querySelectorAll('[role=option]')
                    const matched = Array.from(options).find(el => el.dataset.value === this.state)
                    if (matched) {
                        this.selectedLabel = matched.dataset.label
                    }
                } else {
                    this.selectedLabel = <?php echo \Illuminate\Support\Js::from($placeholder)->toHtml() ?>
                }
            })
        },

        get hasSelection() {
            return this.state !== null && this.state !== undefined && this.state !== ''
        },

        toggle() {
            this.isOpen ? this.close() : this.open()
        },

        open() {
            if (<?php echo \Illuminate\Support\Js::from($disabled)->toHtml() ?>) return
            this.isOpen = true
            this.search = ''
            this.focusedIndex = -1
            this.$nextTick(() => {
                const searchInput = this.$root.querySelector('[data-select-search]')
                if (searchInput) {
                    searchInput.focus()
                } else {
                    this.focusFirst()
                }
            })
        },

        close() {
            this.isOpen = false
            this.search = ''
            this.focusedIndex = -1
        },

        select(value, label) {
            this.state = value
            this.selectedLabel = label
            const hidden = this.$root.querySelector('input[type=hidden]')
            if (hidden) {
                hidden.value = value ?? ''
                hidden.dispatchEvent(new Event('input', { bubbles: true }))
            }
            this.close()
        },

        handleClickAway(target) {
            const trigger = this.$root.querySelector('[data-select-trigger]')
            if (trigger && trigger.contains(target)) return
            this.close()
        },

        get visibleOptions() {
            return Array.from(this.$root.querySelectorAll('[role=option]'))
                .filter(el => el.offsetParent !== null)
        },

        focusFirst() {
            const items = this.visibleOptions
            if (items.length > 0) {
                items[0].focus()
                this.focusedIndex = 0
            }
        },

        focusLast() {
            const items = this.visibleOptions
            if (items.length > 0) {
                items[items.length - 1].focus()
                this.focusedIndex = items.length - 1
            }
        },

        focusNext() {
            const items = this.visibleOptions
            const current = document.activeElement
            const idx = items.indexOf(current)
            const next = items[Math.min(idx + 1, items.length - 1)]
            if (next) {
                next.focus()
                this.focusedIndex = Math.min(idx + 1, items.length - 1)
            }
        },

        focusPrev() {
            const items = this.visibleOptions
            const current = document.activeElement
            const idx = items.indexOf(current)
            const prev = items[Math.max(idx - 1, 0)]
            if (prev) {
                prev.focus()
                this.focusedIndex = Math.max(idx - 1, 0)
            }
        },

        handleOptionKeydown(event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault()
                const option = event.currentTarget
                const value = option.dataset.value
                const label = option.dataset.label
                if (value !== undefined) {
                    this.select(value, label)
                }
            }
            if (event.key === 'Escape') {
                event.preventDefault()
                this.close()
            }
        },
    }"
    x-on:keydown.down.prevent="focusNext()"
    x-on:keydown.up.prevent="focusPrev()"
    x-on:keydown.home.prevent="focusFirst()"
    x-on:keydown.end.prevent="focusLast()"
    x-on:keydown.escape.prevent="close()"
    <?php if($disabled): ?> aria-disabled="true" <?php endif; ?>
    <?php if($invalid): ?> aria-invalid="true" <?php endif; ?>
    role="listbox"
    <?php echo e($attributes->class(['relative w-full'])); ?>

>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($label): ?>
        <label class="mb-2 block text-sm font-medium mine-text-primary">
            <?php echo e($label); ?>

        </label>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($name): ?>
        <input type="hidden" name="<?php echo e($name); ?>" wire:model.defer="<?php echo e($name); ?>" x-bind:value="state" />
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="relative">
        <?php if (isset($component)) { $__componentOriginal58602fdedb57f99a5483fcff112163e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal58602fdedb57f99a5483fcff112163e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.select.trigger','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.select.trigger'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal58602fdedb57f99a5483fcff112163e9)): ?>
<?php $attributes = $__attributesOriginal58602fdedb57f99a5483fcff112163e9; ?>
<?php unset($__attributesOriginal58602fdedb57f99a5483fcff112163e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal58602fdedb57f99a5483fcff112163e9)): ?>
<?php $component = $__componentOriginal58602fdedb57f99a5483fcff112163e9; ?>
<?php unset($__componentOriginal58602fdedb57f99a5483fcff112163e9); ?>
<?php endif; ?>

        <div
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            x-on:click.away="handleClickAway($event.target)"
            class="absolute <?php echo e($positionClasses); ?> z-50 bg-(--mine-input-bg) rounded-xl border-2 border-(--mine-input-border) shadow-lg"
        >
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($searchable): ?>
                <div class="flex items-center gap-2 px-4 py-2 border-b border-(--mine-input-border)">
                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'magnifying-glass','class' => 'size-5 text-(--mine-input-icon) shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'magnifying-glass','class' => 'size-5 text-(--mine-input-icon) shrink-0']); ?>
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
                    <input
                        x-model="search"
                        data-select-search
                        type="text"
                        placeholder="Search..."
                        class="w-full h-8 bg-transparent text-sm mine-text-primary placeholder:text-(--mine-input-placeholder) focus:outline-none focus-visible:outline-none"
                    />
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <ul
                class="max-h-60 overflow-y-auto mine-scrollbar p-1"
                role="listbox"
            >
                <?php echo e($slot); ?>


                <li
                    x-show="isOpen && search !== '' && resultsCount === 0"
                    class="flex items-center justify-center h-14 text-sm mine-text-secondary"
                >
                    No results found
                </li>
            </ul>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="mt-2 text-sm font-medium mine-text-error"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\resources\views/components/mine/select/index.blade.php ENDPATH**/ ?>