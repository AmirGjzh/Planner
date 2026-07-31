<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'mode' => 'single',
    'selectableMonths' => false,
    'selectableYears' => false,
    'yearsRange' => [-10, 10],
    'label' => null,
    'position' => 'bottom-start',
    'height' => 'h-11',
    'showIcon' => false,
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
    'mode' => 'single',
    'selectableMonths' => false,
    'selectableYears' => false,
    'yearsRange' => [-10, 10],
    'label' => null,
    'position' => 'bottom-start',
    'height' => 'h-11',
    'showIcon' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $name = $attributes->whereStartsWith('wire:model')->first() ?? $attributes->whereStartsWith('x-model')->first();

    $positionClasses = match ($position) {
        'bottom-end' => 'top-full mt-1.5 right-0',
        'top-start' => 'bottom-full mb-1.5 left-1/2 -translate-x-1/2',
        'top-end' => 'bottom-full mb-1.5 right-0',
        default => 'top-full mt-1.5 left-1/2 -translate-x-1/2',
    };
?>

<div
    x-data="{
        open: false,
        mode: <?php echo \Illuminate\Support\Js::from($mode)->toHtml() ?>,
        selectableMonths: <?php echo \Illuminate\Support\Js::from((bool) $selectableMonths)->toHtml() ?>,
        selectableYears: <?php echo \Illuminate\Support\Js::from((bool) $selectableYears)->toHtml() ?>,
        yearsRange: <?php echo \Illuminate\Support\Js::from($yearsRange)->toHtml() ?>,
        name: <?php echo \Illuminate\Support\Js::from($name)->toHtml() ?>,
        state: null,
        month: 0,
        year: 0,

        init() {
            const now = new Date()
            now.setHours(0, 0, 0, 0)
            this.month = now.getMonth()
            this.year = now.getFullYear()

            this.$watch('state', () => {
                if (this.open) return
                if (this.mode === 'single' && this.state) {
                    const d = new Date(this.state + 'T00:00:00')
                    if (!isNaN(d.getTime())) {
                        this.month = d.getMonth()
                        this.year = d.getFullYear()
                    }
                }
                if (this.mode === 'range' && this.state?.start) {
                    const d = new Date(this.state.start + 'T00:00:00')
                    if (!isNaN(d.getTime())) {
                        this.month = d.getMonth()
                        this.year = d.getFullYear()
                    }
                }
            })

            if (this.name && typeof $wire !== 'undefined') {
                this.state = $wire.get(this.name)
                $wire.$watch(this.name, (value) => {
                    this.state = value
                })
            }
        },

        get months() {
            return Array.from({ length: 12 }, (_, i) => {
                return new Date(2000, i, 1).toLocaleDateString('default', { month: 'long' })
            })
        },

        get monthName() {
            return this.months[this.month]
        },

        get years() {
            const now = new Date().getFullYear()
            const start = now + this.yearsRange[0]
            const end = now + this.yearsRange[1]
            const result = []
            for (let y = start; y <= end; y++) {
                result.push(y)
            }
            return result
        },

        get dayLabels() {
            const base = new Date(2023, 0, 1)
            base.setDate(base.getDate() + (7 - base.getDay()))
            const formatter = new Intl.DateTimeFormat('default', { weekday: 'short' })
            return Array.from({ length: 7 }, (_, i) => {
                const d = new Date(base)
                d.setDate(base.getDate() + i)
                return formatter.format(d)
            })
        },

        get daysInMonth() {
            return new Date(this.year, this.month + 1, 0).getDate()
        },

        get firstDayOfMonth() {
            return new Date(this.year, this.month, 1).getDay()
        },

        toISODate(date) {
            const y = date.getFullYear()
            const m = String(date.getMonth() + 1).padStart(2, '0')
            const d = String(date.getDate()).padStart(2, '0')
            return `${y}-${m}-${d}`
        },

        isSameDay(a, b) {
            return a.getFullYear() === b.getFullYear() &&
                a.getMonth() === b.getMonth() &&
                a.getDate() === b.getDate()
        },

        get cells() {
            const cells = []
            const totalDays = this.daysInMonth
            const firstDay = this.firstDayOfMonth

            for (let i = 0; i < firstDay; i++) {
                cells.push({ key: 'pre-' + i, blank: true, isInMonth: false, col: cells.length % 7 })
            }

            for (let d = 1; d <= totalDays; d++) {
                const date = new Date(this.year, this.month, d)
                const iso = this.toISODate(date)
                const today = new Date()
                today.setHours(0, 0, 0, 0)
                const isToday = this.isSameDay(date, today)
                let isSelected = false
                let isRangeStart = false
                let isRangeEnd = false
                let isInRange = false

                if (this.mode === 'single') {
                    isSelected = this.state === iso
                } else if (this.mode === 'range' && this.state) {
                    isRangeStart = this.state.start === iso
                    isRangeEnd = this.state.end === iso
                    isSelected = isRangeStart || isRangeEnd
                    if (this.state.start && this.state.end) {
                        isInRange = iso > this.state.start && iso < this.state.end
                    }
                }

                cells.push({
                    key: iso,
                    day: d,
                    iso: iso,
                    blank: false,
                    isInMonth: true,
                    isToday,
                    isSelected,
                    isRangeStart,
                    isRangeEnd,
                    isInRange,
                    col: cells.length % 7,
                })
            }

            const totalCells = firstDay + totalDays
            const rows = Math.ceil(totalCells / 7)
            const remaining = (rows * 7) - cells.length
            for (let i = 0; i < remaining; i++) {
                cells.push({ key: 'post-' + i, blank: true, isInMonth: false })
            }

            return cells
        },

        prevMonth() {
            if (this.month === 0) {
                this.month = 11
                this.year--
            } else {
                this.month--
            }
        },

        nextMonth() {
            if (this.month === 11) {
                this.month = 0
                this.year++
            } else {
                this.month++
            }
        },

        selectDay(cell) {
            if (this.mode === 'single') {
                this.state = this.state === cell.iso ? null : cell.iso
                const hidden = this.$root.querySelector('input[type=hidden]')
                if (hidden) {
                    hidden.value = this.state ?? ''
                    hidden.dispatchEvent(new Event('input', { bubbles: true }))
                }
                this.close()
            } else if (this.mode === 'range') {
                if (!this.state?.start || (this.state.start && this.state.end)) {
                    this.state = { start: cell.iso, end: null }
                } else {
                    let start = this.state.start
                    let end = cell.iso
                    if (end < start) {
                        end = this.state.start
                        start = cell.iso
                    }
                    this.state = { start, end }
                }
            }
        },

        get hasState() {
            if (!this.state) return false
            if (typeof this.state === 'string') return this.state !== ''
            if (typeof this.state === 'object') return !!(this.state.start || this.state.end)
            return false
        },

        get triggerLabel() {
            if (!this.hasState) return 'Select date'
            if (this.mode === 'single') return this.formatDate(this.state)
            if (this.mode === 'range') return this.formatRange(this.state?.start, this.state?.end)
            return 'Select date'
        },

        formatDate(iso) {
            if (!iso) return null
            const date = new Date(iso + 'T00:00:00')
            if (isNaN(date.getTime())) return null
            const today = new Date()
            today.setHours(0, 0, 0, 0)
            const diff = Math.round((date - today) / (1000 * 60 * 60 * 24))
            if (diff === 0) return 'Today'
            if (diff === 1) return 'Tomorrow'
            if (diff === -1) return 'Yesterday'
            return date.toLocaleDateString('default', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
            })
        },

        formatRange(startISO, endISO) {
            if (!startISO) return 'Select range'
            const startDate = new Date(startISO + 'T00:00:00')
            if (!endISO) return this.formatDate(startISO)
            const endDate = new Date(endISO + 'T00:00:00')
            if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) return 'Select range'

            const sameYear = startDate.getFullYear() === endDate.getFullYear()
            const sameMonth = sameYear && startDate.getMonth() === endDate.getMonth()
            const monthDay = (d) => d.toLocaleDateString('default', { day: '2-digit', month: 'short' })
            const fullDate = (d) => d.toLocaleDateString('default', { day: '2-digit', month: 'short', year: 'numeric' })

            if (sameMonth) {
                const monthStr = startDate.toLocaleDateString('default', { month: 'short' })
                const startDay = String(startDate.getDate()).padStart(2, '0')
                const endDay = String(endDate.getDate()).padStart(2, '0')
                return `${monthStr} ${startDay} \u2192 ${endDay}, ${startDate.getFullYear()}`
            }
            if (sameYear) {
                return `${monthDay(startDate)} \u2192 ${monthDay(endDate)}, ${startDate.getFullYear()}`
            }
            return `${fullDate(startDate)} \u2192 ${fullDate(endDate)}`
        },

        toggle() {
            this.open = !this.open
        },

        close() {
            this.open = false
        },
    }"
    x-on:click.away="close()"
    x-on:keydown.escape.window="close()"
    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
        'relative w-full',
    ]); ?>"
    data-slot="datepicker"
    <?php echo e($attributes->class(['w-full'])); ?>

>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($label): ?>
        <label class="mb-2 block text-sm font-medium mine-text-primary">
            <?php echo e($label); ?>

        </label>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="relative">
        <input type="hidden" name="<?php echo e($name); ?>" wire:model.defer="<?php echo e($name); ?>" x-bind:value="state" />

        <button
            type="button"
            x-on:click="toggle()"
            data-datepicker-trigger
            :data-open="open"
            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'flex items-center justify-between w-full px-4 rounded-xl border-2 bg-(--mine-input-bg) transition-all duration-200 ease-out cursor-pointer',
                $height => true,
                'border-(--mine-input-border) data-open:border-(--mine-input-border-focus) focus-visible:border-(--mine-input-border-focus)',
                'data-open:ring-4 data-open:ring-(--mine-input-ring-focus)',
                'focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-(--mine-input-ring-focus)',
            ]); ?>"
        >
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showIcon): ?>
                <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'calendar','variant' => 'mini','class' => 'size-5 text-(--mine-input-icon) shrink-0 mr-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'calendar','variant' => 'mini','class' => 'size-5 text-(--mine-input-icon) shrink-0 mr-2']); ?>
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

            <span
                x-text="triggerLabel"
                class="flex-1 text-sm truncate text-left <?php echo e($showIcon ? 'mx-2' : 'mr-2'); ?>"
                :class="hasState ? 'mine-text-primary' : 'text-(--mine-input-placeholder)'"
            ></span>

            <div :class="open ? 'rotate-180' : ''" class="shrink-0 transition-transform duration-200">
                <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'chevron-up-down','variant' => 'mini','class' => 'size-5 text-(--mine-input-icon)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-up-down','variant' => 'mini','class' => 'size-5 text-(--mine-input-icon)']); ?>
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
        </button>

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute <?php echo e($positionClasses); ?> z-50 bg-(--mine-input-bg) rounded-xl border-2 border-(--mine-input-border) shadow-lg p-4"
            x-cloak
        >
            <div class="flex items-center justify-between mb-2">
                <button
                    type="button"
                    x-on:click="prevMonth()"
                    class="p-1.5 rounded-lg mine-btn-icon transition-colors duration-200 mine-text-secondary focus-visible:outline-none"
                >
                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'chevron-left','variant' => 'mini','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-left','variant' => 'mini','class' => 'size-4']); ?>
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

                <div class="flex items-center gap-3">
                    <template x-if="selectableMonths">
                        <?php if (isset($component)) { $__componentOriginalf42de91da559418a1082b0941b90bf4b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf42de91da559418a1082b0941b90bf4b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.datepicker.select','data' => ['label' => 'monthName']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.datepicker.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'monthName']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <template x-for="(m, i) in months" :key="i">
                                <button
                                    type="button"
                                    @click="month = i; open = false"
                                    :class="month === i
                                        ? 'bg-(--mine-select-selected-bg) text-(--mine-select-selected-text) font-medium'
                                        : 'hover:bg-(--mine-select-bg-hover) mine-text-primary'"
                                    class="flex items-center w-full px-3 py-1.5 rounded-lg text-sm text-left transition-colors duration-200 hover:cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-(--mine-select-ring-focus)"
                                >
                                    <span x-text="m"></span>
                                </button>
                            </template>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf42de91da559418a1082b0941b90bf4b)): ?>
<?php $attributes = $__attributesOriginalf42de91da559418a1082b0941b90bf4b; ?>
<?php unset($__attributesOriginalf42de91da559418a1082b0941b90bf4b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf42de91da559418a1082b0941b90bf4b)): ?>
<?php $component = $__componentOriginalf42de91da559418a1082b0941b90bf4b; ?>
<?php unset($__componentOriginalf42de91da559418a1082b0941b90bf4b); ?>
<?php endif; ?>
                    </template>
                    <template x-if="!selectableMonths">
                        <span class="text-sm font-semibold mine-text-primary" x-text="monthName"></span>
                    </template>

                    <template x-if="selectableYears">
                        <?php if (isset($component)) { $__componentOriginalf42de91da559418a1082b0941b90bf4b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf42de91da559418a1082b0941b90bf4b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.datepicker.select','data' => ['label' => 'year']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.datepicker.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('year')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <template x-for="y in years" :key="y">
                                <button
                                    type="button"
                                    @click="year = y; open = false"
                                    :class="year === y
                                        ? 'bg-(--mine-select-selected-bg) text-(--mine-select-selected-text) font-medium'
                                        : 'hover:bg-(--mine-select-bg-hover) mine-text-primary'"
                                    class="flex items-center w-full px-3 py-1.5 rounded-lg text-sm text-left transition-colors duration-200 hover:cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-(--mine-select-ring-focus)"
                                >
                                    <span x-text="y"></span>
                                </button>
                            </template>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf42de91da559418a1082b0941b90bf4b)): ?>
<?php $attributes = $__attributesOriginalf42de91da559418a1082b0941b90bf4b; ?>
<?php unset($__attributesOriginalf42de91da559418a1082b0941b90bf4b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf42de91da559418a1082b0941b90bf4b)): ?>
<?php $component = $__componentOriginalf42de91da559418a1082b0941b90bf4b; ?>
<?php unset($__componentOriginalf42de91da559418a1082b0941b90bf4b); ?>
<?php endif; ?>
                    </template>
                    <template x-if="!selectableYears">
                        <span class="text-sm font-medium mine-text-secondary" x-text="year"></span>
                    </template>
                </div>

                <button
                    type="button"
                    x-on:click="nextMonth()"
                    class="p-1.5 rounded-lg mine-btn-icon transition-colors duration-200 mine-text-secondary focus-visible:outline-none"
                >
                    <?php if (isset($component)) { $__componentOriginale972b8dab630a05a3405c81d7f2bc7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale972b8dab630a05a3405c81d7f2bc7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mine.icon.index','data' => ['name' => 'chevron-right','variant' => 'mini','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mine.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-right','variant' => 'mini','class' => 'size-4']); ?>
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

            <div class="grid justify-items-center grid-cols-7 mb-1">
                <template x-for="day in dayLabels" :key="day">
                    <div class="flex items-center justify-center h-8">
                        <span class="text-xs font-medium mine-text-secondary" x-text="day"></span>
                    </div>
                </template>
            </div>

            <div class="grid justify-items-center grid-cols-7">
                <template x-for="cell in cells" :key="cell.key">
                    <div>
                        <template x-if="cell.blank">
                            <div class="flex items-center justify-center h-11 w-11 mx-auto"></div>
                        </template>
                        <template x-if="!cell.blank">
                            <button
                                type="button"
                                x-on:click="selectDay(cell)"
                                :class="{
                                    'bg-(--mine-datepicker-pill-bg) text-(--mine-datepicker-pill-text) hover:bg-(--mine-datepicker-pill-bg-hover) shadow-sm font-bold z-40': cell.isRangeStart || cell.isRangeEnd,
                                    'hover:bg-(--mine-datepicker-day-bg-hover) mine-text-primary': !cell.isSelected && !cell.isInRange,
                                    'text-(--mine-datepicker-day-dim-text)': !cell.isInMonth && !cell.isSelected && !cell.isInRange,
                                    'bg-(--mine-datepicker-day-selected-bg) text-(--mine-datepicker-day-selected-text) hover:bg-(--mine-datepicker-day-selected-bg-hover) shadow-sm font-bold': cell.isSelected && !cell.isRangeStart && !cell.isRangeEnd
                                }"
                                class="flex items-center justify-center h-11 w-11 hover:cursor-pointer mx-auto rounded-lg text-sm transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-(--mine-datepicker-day-ring-focus)"
                            >
                                <span
                                    :class="cell.isInRange && !cell.isRangeStart && !cell.isRangeEnd
                                        ? 'flex items-center justify-center h-9 w-full bg-(--mine-datepicker-pill-between-bg) text-(--mine-datepicker-pill-between-text) ' + (cell.col === 0 ? 'rounded-s-lg' : cell.col === 6 ? 'rounded-e-lg' : 'rounded-none')
                                        : ''"
                                >
                                    <span
                                        x-text="cell.day"
                                    ></span>
                                </span>
                            </button>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\AmirMohammad\Programming\Laravel\Planner\resources\views/components/mine/datepicker/index.blade.php ENDPATH**/ ?>