<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'triggerMode' => 'click',
    'group' => null,
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
    'triggerMode' => 'click',
    'group' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div
    x-data="{
        open: false,
        triggerMode: <?php echo \Illuminate\Support\Js::from($triggerMode)->toHtml() ?>,
        group: <?php echo \Illuminate\Support\Js::from($group)->toHtml() ?>,
        hoverTimeout: null,
        init() {
            if (this.group) {
                window.addEventListener('close-dropdowns-'+this.group, () => this.close())
            }
        },
        show() {
            if (this.triggerMode !== 'hover') return
            this.cancelHide()
            if (this.group && !this.open) {
                window.dispatchEvent(new CustomEvent('close-dropdowns-'+this.group))
            }
            this.open = true
        },
        hide(delay = 0) {
            if (this.triggerMode !== 'hover') return
            this.hoverTimeout = setTimeout(() => {
                this.open = false
            }, delay)
        },
        cancelHide() {
            if (this.hoverTimeout) {
                clearTimeout(this.hoverTimeout)
                this.hoverTimeout = null
            }
        },
        toggle() {
            if (this.triggerMode === 'hover') {
                this.show()
                return
            }
            if (this.group && !this.open) {
                window.dispatchEvent(new CustomEvent('close-dropdowns-'+this.group))
            }
            this.open = !this.open
        },
        close() {
            this.cancelHide()
            this.open = false
        },
        focusNext() {
            const items = this.$el.querySelectorAll('[role=&quot;menuitem&quot;]:not([disabled])')
            const current = document.activeElement
            const idx = Array.from(items).indexOf(current)
            const next = items[Math.min(idx + 1, items.length - 1)]
            next?.focus()
        },
        focusPrev() {
            const items = this.$el.querySelectorAll('[role=&quot;menuitem&quot;]:not([disabled])')
            const current = document.activeElement
            const idx = Array.from(items).indexOf(current)
            const prev = items[Math.max(idx - 1, 0)]
            prev?.focus()
        },
        focusFirst() {
            const items = this.$el.querySelectorAll('[role=&quot;menuitem&quot;]:not([disabled])')
            items[0]?.focus()
        },
        focusLast() {
            const items = this.$el.querySelectorAll('[role=&quot;menuitem&quot;]:not([disabled])')
            items[items.length - 1]?.focus()
        }
    }"
    x-on:keydown.escape.window="close()"
    x-on:click.outside="close()"
    <?php echo e($attributes->merge(['class' => 'relative'])); ?>

>
    <?php echo e($slot); ?>

</div>
<?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\resources\views/components/mine/dropdown/index.blade.php ENDPATH**/ ?>