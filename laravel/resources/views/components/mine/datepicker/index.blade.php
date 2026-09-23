@props([
    'mode' => 'single',
    'selectableMonths' => false,
    'selectableYears' => false,
    'yearsRange' => [-10, 10],
    'label' => null,
    'position' => 'bottom-start',
    'height' => 'h-11',
    'showIcon' => false,
    'leftIcon' => null,
])

@php
    $name = $attributes->whereStartsWith('wire:model')->first() ?? $attributes->whereStartsWith('x-model')->first();

    $hasError = $name !== null && $errors->has($name);

    $positionClasses = match ($position) {
        'bottom-end' => 'top-full mt-1.5 right-0',
        'top-start' => 'bottom-full mb-1.5 left-0',
        'top-end' => 'bottom-full mb-1.5 right-0',
        default => 'top-full mt-1.5 left-0',
    };
@endphp

<div
    x-data="{
        open: false,
        mode: @js($mode),
        selectableMonths: @js((bool) $selectableMonths),
        selectableYears: @js((bool) $selectableYears),
        yearsRange: @js($yearsRange),
        name: @js($name),
        fa: @js(app()->isLocale('fa')),
        t: @js([
            'select_date' => __('Select a date'),
            'select_range' => __('Select a range'),
            'today' => __('Today'),
            'tomorrow' => __('Tomorrow'),
            'yesterday' => __('Yesterday'),
        ]),
        state: null,
        anchor: new Date(),

        init() {
            const now = new Date()
            now.setHours(0, 0, 0, 0)
            this.anchor = this.fa
                ? Jalali.toGregorian(Jalali.getPersian(now).year, Jalali.getPersian(now).month, 1)
                : new Date(now.getFullYear(), now.getMonth(), 1)

            this.$watch('state', () => {
                if (this.open) return
                if (this.mode === 'single' && this.state) {
                    const d = new Date(this.state + 'T00:00:00')
                    if (! isNaN(d.getTime())) {
                        this.anchor = this.fa
                            ? Jalali.toGregorian(Jalali.getPersian(d).year, Jalali.getPersian(d).month, 1)
                            : new Date(d.getFullYear(), d.getMonth(), 1)
                    }
                }
                if (this.mode === 'range' && this.state?.start) {
                    const d = new Date(this.state.start + 'T00:00:00')
                    if (! isNaN(d.getTime())) {
                        this.anchor = this.fa
                            ? Jalali.toGregorian(Jalali.getPersian(d).year, Jalali.getPersian(d).month, 1)
                            : new Date(d.getFullYear(), d.getMonth(), 1)
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
            if (this.fa) {
                return Jalali.monthAnchors(this.anchor).map((anchor) => Jalali.monthName(anchor))
            }

            return Array.from({ length: 12 }, (_, i) => {
                return new Date(2000, i, 1).toLocaleDateString('default', { month: 'long' })
            })
        },

        get monthName() {
            if (this.fa) return Jalali.monthName(this.anchor)

            return this.months[this.anchor.getMonth()]
        },

        get currentMonthIndex() {
            return this.fa ? Jalali.getPersian(this.anchor).month - 1 : this.anchor.getMonth()
        },

        get currentYear() {
            return this.fa ? Jalali.getPersian(this.anchor).year : this.anchor.getFullYear()
        },

        get yearLabel() {
            return this.fa ? Jalali.yearLabel(this.anchor) : String(this.anchor.getFullYear())
        },

        get years() {
            const baseYear = this.fa ? Jalali.getPersian(new Date()).year : new Date().getFullYear()
            const start = baseYear + this.yearsRange[0]
            const end = baseYear + this.yearsRange[1]
            const result = []
            for (let y = start; y <= end; y++) {
                result.push({ value: y, label: this.fa ? Jalali.toFaDigits(y) : String(y) })
            }
            return result
        },

        get dayLabels() {
            if (this.fa) return Jalali.weekdayLabels()

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
            if (this.fa) return Jalali.persianMonthLength(this.anchor)

            return new Date(this.anchor.getFullYear(), this.anchor.getMonth() + 1, 0).getDate()
        },

        get firstDayOfMonth() {
            if (this.fa) return Jalali.firstDayOffset(this.anchor)

            return this.anchor.getDay()
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
                const date = this.fa
                    ? Jalali.addDays(this.anchor, d - 1)
                    : new Date(this.anchor.getFullYear(), this.anchor.getMonth(), d)
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
                    dayText: this.fa ? Jalali.dayNumber(date) : d,
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
            this.anchor = this.fa
                ? Jalali.addDays(this.anchor, -this.daysInMonth)
                : new Date(this.anchor.getFullYear(), this.anchor.getMonth() - 1, 1)
        },

        nextMonth() {
            this.anchor = this.fa
                ? Jalali.addDays(this.anchor, this.daysInMonth)
                : new Date(this.anchor.getFullYear(), this.anchor.getMonth() + 1, 1)
        },

        setMonth(index) {
            this.anchor = this.fa
                ? Jalali.monthAnchors(this.anchor)[index]
                : new Date(this.anchor.getFullYear(), index, 1)
        },

        setYear(value) {
            if (this.fa) {
                const month = Jalali.getPersian(this.anchor).month
                this.anchor = Jalali.toGregorian(value, month, 1)
            } else {
                this.anchor = new Date(value, this.anchor.getMonth(), 1)
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
                if (! this.state?.start || (this.state.start && this.state.end)) {
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

                if (this.name && typeof $wire !== 'undefined' && this.state?.start && this.state?.end) {
                    $wire.set(this.name, this.state, false)
                }
            }
        },

        get hasState() {
            if (! this.state) return false
            if (typeof this.state === 'string') return this.state !== ''
            if (typeof this.state === 'object') return !!(this.state.start || this.state.end)
            return false
        },

        get triggerLabel() {
            if (! this.hasState) return this.mode === 'range' ? (this.fa ? this.t.select_range : 'Select a range') : (this.fa ? this.t.select_date : 'Select a date')
            if (this.mode === 'single') return this.formatDate(this.state)
            if (this.mode === 'range') return this.formatRange(this.state?.start, this.state?.end)
            return this.fa ? this.t.select_date : 'Select a date'
        },

        formatDate(iso) {
            if (! iso) return null
            const date = new Date(iso + 'T00:00:00')
            if (isNaN(date.getTime())) return null
            const today = new Date()
            today.setHours(0, 0, 0, 0)
            const diff = Math.round((date - today) / (1000 * 60 * 60 * 24))
            if (diff === 0) return this.fa ? this.t.today : 'Today'
            if (diff === 1) return this.fa ? this.t.tomorrow : 'Tomorrow'
            if (diff === -1) return this.fa ? this.t.yesterday : 'Yesterday'
            if (this.fa) return Jalali.formatDate(iso)
            return date.toLocaleDateString('default', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
            })
        },

        formatRange(startISO, endISO) {
            if (! startISO) return this.fa ? this.t.select_range : 'Select a range'
            const startDate = new Date(startISO + 'T00:00:00')
            if (! endISO) return this.formatDate(startISO)
            const endDate = new Date(endISO + 'T00:00:00')
            if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) return this.fa ? this.t.select_range : 'Select a range'

            if (this.fa) {
                const start = Jalali.getPersian(startDate)
                const end = Jalali.getPersian(endDate)
                if (start.year === end.year && start.month === end.month) {
                    return `${Jalali.monthName(startDate)} ${Jalali.toFaDigits(start.day)} \u2192 ${Jalali.toFaDigits(end.day)}، ${Jalali.yearLabel(startDate)}`
                }
                if (start.year === end.year) {
                    return `${Jalali.formatMonthDay(startDate)} \u2192 ${Jalali.formatMonthDay(endDate)}، ${Jalali.yearLabel(startDate)}`
                }
                return `${Jalali.formatLong(startDate)} \u2192 ${Jalali.formatLong(endDate)}`
            }

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
            this.open = ! this.open
        },

        close() {
            this.open = false
        },
    }"
    x-on:click.away="close()"
    x-on:keydown.escape.window="close()"
    @class([
        'relative w-full',
    ])
    data-slot="datepicker"
    {{ $attributes->class(['w-full']) }}
>
    @if ($label)
        <label class="mine-text-primary mb-2 block text-sm font-medium"> {{ $label }} </label>
    @endif

    <div class="relative">
        <input type="hidden" name="{{ $name }}" wire:model.defer="{{ $name }}" x-bind:value="state" />

        <button
            type="button"
            x-on:click="toggle()"
            data-datepicker-trigger
            :data-open="open"
            @class([
                'group flex items-center justify-between w-full px-4 rounded-xl border-2 transition-all duration-200 ease-out cursor-pointer',
                $height => true,
                'bg-(--mine-input-bg)' => ! $hasError,
                'bg-(--mine-input-error-bg)' => $hasError,
                'border-(--mine-input-border)' => ! $hasError,
                'border-(--mine-input-error-border)' => $hasError,
                'data-open:border-(--mine-input-border-focus) focus-visible:border-(--mine-input-border-focus)' => ! $hasError,
                'data-open:border-(--mine-input-error-border) focus-visible:border-(--mine-input-error-border)' => $hasError,
                'data-open:ring-4 data-open:ring-(--mine-input-ring-focus)' => ! $hasError,
                'data-open:ring-4 data-open:ring-(--mine-input-error-ring)' => $hasError,
                'focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-(--mine-input-ring-focus)' => ! $hasError,
                'focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-(--mine-input-error-ring)' => $hasError,
            ])
        >
            @if ($leftIcon)
                <x-mine.icon
                    :name="$leftIcon"
                    size="20"
                    weight="filled"
                    @class([
                        'mr-2' => app()->isLocale('en'),
                        'ml-2' => app()->isLocale('fa'),
                        'size-5 shrink-0',
                        'text-(--mine-input-icon)' => ! $hasError,
                        'text-(--mine-input-error-icon)' => $hasError,
                        'group-data-open:text-(--mine-input-border-focus)' => ! $hasError,
                        'group-data-open:text-(--mine-input-error-border)' => $hasError,
                    ])
                />
            @elseif ($showIcon)
                <x-mine.icon
                    name="calendar"
                    variant="mini"
                    @class([
                        'mr-2' => app()->isLocale('en'),
                        'ml-2' => app()->isLocale('fa'),
                        'size-5 shrink-0',
                        'text-(--mine-input-icon)' => ! $hasError,
                        'text-(--mine-input-error-icon)' => $hasError,
                        'group-data-open:text-(--mine-input-border-focus)' => ! $hasError,
                        'group-data-open:text-(--mine-input-error-border)' => $hasError,
                    ])
                />
            @endif

            <span
                x-text="triggerLabel"
                class="flex-1 text-sm truncate {{ app()->isLocale('fa') ? 'text-right' : 'text-left' }} pt-1 {{ ($leftIcon || $showIcon) ? 'mx-2' : (app()->isLocale('en') ? 'mr-2' : 'ml-2') }}"
                :class="hasState ? 'mine-text-primary' : 'text-(--mine-input-placeholder)'"
            ></span>

            <div
                :class="open ? 'rotate-180' : ''"
                @class([
                    '-mr-1' => app()->isLocale('en'),
                    '-ml-1' => app()->isLocale('fa'),
                    'shrink-0 transition-transform duration-200',
                ])
            >
                <x-mine.icon
                    name="ChevronExpandY"
                    size="16"
                    weight="filled"
                    @class([
                        'text-(--mine-input-icon)' => ! $hasError,
                        'text-(--mine-input-error-icon)' => $hasError,
                    ])
                />
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
            class="absolute {{ $positionClasses }} z-50 bg-(--mine-input-bg) rounded-xl border-2 border-(--mine-input-border) shadow-lg p-4"
            x-cloak
        >
            <div class="mb-2 flex items-center justify-between">
                <button
                    type="button"
                    x-on:click="prevMonth()"
                    class="mine-btn-icon mine-text-secondary rounded-lg p-1.5 transition-colors duration-200 focus-visible:outline-none"
                >
                    <x-mine.icon name="Angle{{ app()->isLocale('en') ? 'Left' : 'Right' }}" weight="filled" size="16" />
                </button>

                <div class="flex items-center gap-3">
                    <template x-if="selectableMonths">
                        <x-mine.datepicker.select label="monthName">
                            <template x-for="(m, i) in months" :key="i">
                                <button
                                    type="button"
                                    @click="
                                        setMonth(i);
                                        open = false;
                                    "
                                    :class="currentMonthIndex === i
                                        ? 'bg-(--mine-select-selected-bg) text-(--mine-select-selected-text) font-medium'
                                        : 'hover:bg-(--mine-select-bg-hover) mine-text-primary'"
                                    class="flex items-center w-full px-3 py-1.5 rounded-lg text-sm {{ app()->isLocale('fa') ? 'text-right' : 'text-left' }} transition-colors duration-200 hover:cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-(--mine-select-ring-focus)"
                                >
                                    <span x-text="m"></span>
                                </button>
                            </template>
                        </x-mine.datepicker.select>
                    </template>
                    <template x-if="! selectableMonths">
                        <span class="mine-text-primary text-sm font-semibold" x-text="monthName"></span>
                    </template>

                    <template x-if="selectableYears">
                        <x-mine.datepicker.select label="yearLabel">
                            <template x-for="y in years" :key="y.value">
                                <button
                                    type="button"
                                    @click="
                                        setYear(y.value);
                                        open = false;
                                    "
                                    :class="currentYear === y.value
                                        ? 'bg-(--mine-select-selected-bg) text-(--mine-select-selected-text) font-medium'
                                        : 'hover:bg-(--mine-select-bg-hover) mine-text-primary'"
                                    class="flex items-center w-full px-3 py-1.5 rounded-lg text-sm {{ app()->isLocale('fa') ? 'text-right' : 'text-left' }} transition-colors duration-200 hover:cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-(--mine-select-ring-focus)"
                                >
                                    <span x-text="y.label"></span>
                                </button>
                            </template>
                        </x-mine.datepicker.select>
                    </template>
                    <template x-if="! selectableYears">
                        <span class="mine-text-secondary text-sm font-medium" x-text="yearLabel"></span>
                    </template>
                </div>

                <button
                    type="button"
                    x-on:click="nextMonth()"
                    class="mine-btn-icon mine-text-secondary rounded-lg p-1.5 transition-colors duration-200 focus-visible:outline-none"
                >
                    <x-mine.icon name="Angle{{ app()->isLocale('fa') ? 'Left' : 'Right' }}" weight="filled" size="16" />
                </button>
            </div>

            <div class="mb-6 grid grid-cols-7 justify-items-center">
                <template x-for="day in dayLabels" :key="day">
                    <div class="flex h-8 items-center justify-center">
                        <span class="mine-text-secondary text-xs font-medium" x-text="day"></span>
                    </div>
                </template>
            </div>

            <div class="grid grid-cols-7 justify-items-center">
                <template x-for="cell in cells" :key="cell.key">
                    <div>
                        <template x-if="cell.blank">
                            <div class="mx-auto flex h-11 w-11 items-center justify-center"></div>
                        </template>
                        <template x-if="! cell.blank">
                            <button
                                type="button"
                                x-on:click="selectDay(cell)"
                                :class="{
                                    'bg-(--mine-datepicker-pill-bg) text-(--mine-datepicker-pill-text) hover:bg-(--mine-datepicker-pill-bg-hover) shadow-sm font-semibold relative z-40':
                                        cell.isRangeStart || cell.isRangeEnd,
                                    'hover:bg-(--mine-datepicker-day-bg-hover) mine-text-primary relative':
                                        ! cell.isSelected && ! cell.isInRange,
                                    'text-(--mine-datepicker-day-dim-text) relative':
                                        ! cell.isInMonth && ! cell.isSelected && ! cell.isInRange,
                                    'bg-(--mine-datepicker-day-selected-bg) text-(--mine-datepicker-day-selected-text) hover:bg-(--mine-datepicker-day-selected-bg-hover) shadow-sm font-semibold relative z-40':
                                        cell.isSelected && ! cell.isRangeStart && ! cell.isRangeEnd,
                                }"
                                class="mx-auto flex h-11 w-11 items-center justify-center rounded-lg text-sm transition-colors duration-200 hover:cursor-pointer focus-visible:ring-2 focus-visible:ring-(--mine-datepicker-day-ring-focus) focus-visible:outline-none"
                            >
                                <span
                                    class="flex w-full items-center justify-center"
                                    :class="cell.isInRange && ! cell.isRangeStart && ! cell.isRangeEnd
                                        ? 'relative z-30 h-9 bg-(--mine-datepicker-pill-between-bg) text-(--mine-datepicker-pill-between-text) ' +
                                          (cell.col === 0
                                              ? 'rounded-s-lg'
                                              : cell.col === 6
                                                ? 'rounded-e-lg'
                                                : 'rounded-none')
                                        : ''"
                                >
                                    <span class="pt-1" x-text="cell.dayText"></span>
                                </span>
                            </button>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @if ($name)
        <x-mine.input.error :name="$name" />
    @endif
</div>
