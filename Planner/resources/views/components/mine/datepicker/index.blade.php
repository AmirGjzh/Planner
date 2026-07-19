@props([
    'mode' => 'single',
    'selectableMonths' => false,
    'selectableYears' => false,
    'yearsRange' => [-10, 10],
    'label' => null,
    'position' => 'bottom-start',
    'height' => 'h-11',
    'showIcon' => false,
])

@php
    $name = $attributes->whereStartsWith('wire:model')->first() ?? $attributes->whereStartsWith('x-model')->first();

    $positionClasses = match ($position) {
        'bottom-end' => 'top-full mt-1.5 right-0',
        'top-start' => 'bottom-full mb-1.5 left-1/2 -translate-x-1/2',
        'top-end' => 'bottom-full mb-1.5 right-0',
        default => 'top-full mt-1.5 left-1/2 -translate-x-1/2',
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
                cells.push({ key: 'pre-' + i, blank: true, isInMonth: false })
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
    @class([
        'relative w-full',
    ])
    data-slot="datepicker"
    {{ $attributes->class(['w-full']) }}
>
    @if($label)
        <label class="mb-2 block text-sm font-medium mine-text-primary">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        <input type="hidden" name="{{ $name }}" wire:model.defer="{{ $name }}" x-bind:value="state" />

        <button
            type="button"
            x-on:click="toggle()"
            data-datepicker-trigger
            :data-open="open"
            @class([
                'flex items-center justify-between w-full px-4 rounded-xl border-2 bg-[var(--mine-input-bg)] transition-all duration-200 ease-out cursor-pointer',
                $height => true,
                'border-[var(--mine-input-border)] data-open:border-[var(--mine-input-border-focus)]',
                'data-open:ring-4 data-open:ring-[var(--mine-input-ring-focus)]',
                'focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[var(--mine-input-ring-focus)]',
            ])
        >
            @if($showIcon)
                <x-mine.icon name="calendar" variant="mini" class="size-5 text-[var(--mine-input-icon)] shrink-0 mr-2" />
            @endif

            <span
                x-text="triggerLabel"
                class="flex-1 text-sm truncate text-left {{ $showIcon ? 'mx-2' : 'mr-2' }}"
                :class="hasState ? 'mine-text-primary' : 'text-[var(--mine-input-placeholder)]'"
            ></span>

            <div :class="open ? 'rotate-180' : ''" class="shrink-0 transition-transform duration-200">
                <x-mine.icon name="chevron-up-down" variant="mini" class="size-5 text-[var(--mine-input-icon)]" />
            </div>
        </button>

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute {{ $positionClasses }} overflow-x-auto z-50 bg-[var(--mine-input-bg)] rounded-xl border-2 border-[var(--mine-input-border)] shadow-lg p-4"
            x-cloak
        >
            <div class="flex items-center justify-between mb-2">
                <button
                    type="button"
                    x-on:click="prevMonth()"
                    class="p-1.5 rounded-lg hover:cursor-pointer hover:bg-[var(--mine-btn-arrow-bg-hover)] transition-colors duration-200 text-secondary focus-visible:outline-none"
                >
                    <x-mine.icon name="chevron-left" variant="mini" class="size-4" />
                </button>

                <div class="flex items-center gap-1.5">
                    <template x-if="selectableMonths">
                        <select
                            x-model="month"
                            x-on:input="month = parseInt($event.target.value)"
                            class="h-8 py-0 border-0 text-sm appearance-none rounded-lg bg-[var(--mine-nav-link-bg-hover)] px-2 mine-text-primary font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mine-btn-primary-ring-focus)]"
                        >
                            <template x-for="(m, i) in months" :key="i">
                                <option x-text="m" :value="i"></option>
                            </template>
                        </select>
                    </template>
                    <template x-if="!selectableMonths">
                        <span class="text-sm font-semibold mine-text-primary" x-text="monthName"></span>
                    </template>

                    <template x-if="selectableYears">
                        <select
                            x-model="year"
                            x-on:input="year = parseInt($event.target.value)"
                            class="h-8 py-0 border-0 text-sm appearance-none rounded-lg bg-[var(--mine-nav-link-bg-hover)] px-2 mine-text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mine-btn-primary-ring-focus)]"
                        >
                            <template x-for="y in years" :key="y">
                                <option x-text="y" :value="y"></option>
                            </template>
                        </select>
                    </template>
                    <template x-if="!selectableYears">
                        <span class="text-sm text-secondary" x-text="year"></span>
                    </template>
                </div>

                <button
                    type="button"
                    x-on:click="nextMonth()"
                    class="p-1.5 rounded-lg hover:cursor-pointer hover:bg-[var(--mine-btn-arrow-bg-hover)] transition-colors duration-200 text-secondary focus-visible:outline-none"
                >
                    <x-mine.icon name="chevron-right" variant="mini" class="size-4" />
                </button>
            </div>

            <div class="grid justify-items-center grid-cols-7 mb-1">
                <template x-for="day in dayLabels" :key="day">
                    <div class="flex items-center justify-center h-8">
                        <span class="text-xs font-medium text-secondary" x-text="day"></span>
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
                                    'bg-[var(--mine-datepicker-pill-bg)] text-[var(--mine-datepicker-pill-text)] hover:bg-[var(--mine-datepicker-pill-bg-hover)] shadow-sm font-bold': cell.isRangeStart || cell.isRangeEnd,
                                    'bg-[var(--mine-datepicker-pill-between-bg)]': cell.isInRange && !cell.isRangeStart && !cell.isRangeEnd,
                                    'hover:bg-[var(--mine-datepicker-day-bg-hover)] mine-text-primary': !cell.isSelected && !cell.isInRange,
                                    'text-[var(--mine-datepicker-day-dim-text)]': !cell.isInMonth && !cell.isSelected && !cell.isInRange,
                                    'bg-[var(--mine-datepicker-day-selected-bg)] text-[var(--mine-datepicker-day-selected-text)] hover:bg-[var(--mine-datepicker-day-selected-bg-hover)] shadow-sm font-bold': cell.isSelected && !cell.isRangeStart && !cell.isRangeEnd
                                }"
                                class="flex items-center justify-center h-11 w-11 hover:cursor-pointer mx-auto rounded-lg text-sm transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mine-datepicker-day-ring-focus)]"
                            >
                                <span
                                    x-text="cell.day"
                                ></span>
                            </button>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
