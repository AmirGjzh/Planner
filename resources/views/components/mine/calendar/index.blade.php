@props([
    'mode' => 'range',
    'card' => true,
    'island' => null,
])

@php
    $name = $attributes->whereStartsWith('wire:model')->first();
@endphp

<div
    x-data="{
        mode: @js($mode),
        name: @js($name),
        island: @js($island),
        state: null,
        month: 0,
        year: 0,

        init() {
            const now = new Date()
            now.setHours(0, 0, 0, 0)
            this.month = now.getMonth()
            this.year = now.getFullYear()

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

        commit() {
            if (this.name && typeof $wire !== 'undefined') {
                if (this.island) {
                    $wire.$island(this.island).$set(this.name, this.state, true)
                } else {
                    $wire.set(this.name, this.state, true)
                }
            }
        },

        selectDay(cell) {
            if (this.mode === 'single') {
                this.state = this.state === cell.iso ? null : cell.iso
                this.commit()
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

                // Only notify after both start and end are selected
                if (this.state?.start && this.state?.end) {
                    this.commit()
                }
            }
        },

        reset() {
            this.state = null
            this.commit()
        },

        get hasState() {
            if (!this.state) return false
            if (typeof this.state === 'string') return this.state !== ''
            if (typeof this.state === 'object') return !!(this.state.start || this.state.end)
            return false
        },
    }"
    data-slot="calendar"
    {{ $attributes->merge(['class' => 'w-full']) }}
>
    <div @class(['p-4', 'mine-card' => $card])>
        <div class="flex items-center justify-between mb-2">
            <button
                type="button"
                x-on:click="prevMonth()"
                class="p-1.5 rounded-lg mine-btn-icon transition-colors duration-200 mine-text-secondary focus-visible:outline-none"
            >
                <x-mine.icon name="chevron-left" variant="mini" class="size-4" />
            </button>

            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold mine-text-primary" x-text="monthName"></span>
                <span class="text-sm font-medium mine-text-secondary" x-text="year"></span>
            </div>

            <button
                type="button"
                x-on:click="nextMonth()"
                class="p-1.5 rounded-lg mine-btn-icon transition-colors duration-200 mine-text-secondary focus-visible:outline-none"
            >
                <x-mine.icon name="chevron-right" variant="mini" class="size-4" />
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
                                'bg-(--mine-datepicker-pill-bg) text-(--mine-datepicker-pill-text) hover:bg-(--mine-datepicker-pill-bg-hover) shadow-sm font-bold relative z-40': cell.isRangeStart || cell.isRangeEnd,
                                'hover:bg-(--mine-datepicker-day-bg-hover) mine-text-primary relative': !cell.isSelected && !cell.isInRange,
                                'text-(--mine-datepicker-day-dim-text) relative': !cell.isInMonth && !cell.isSelected && !cell.isInRange,
                                'bg-(--mine-datepicker-day-selected-bg) text-(--mine-datepicker-day-selected-text) hover:bg-(--mine-datepicker-day-selected-bg-hover) shadow-sm font-bold relative z-40': cell.isSelected && !cell.isRangeStart && !cell.isRangeEnd
                            }"
                            class="flex items-center justify-center h-11 w-11 hover:cursor-pointer mx-auto rounded-lg text-sm transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-(--mine-datepicker-day-ring-focus)"
                        >
                            <span
                                :class="cell.isInRange && !cell.isRangeStart && !cell.isRangeEnd
                                    ? 'relative z-30 flex items-center justify-center h-9 w-full bg-(--mine-datepicker-pill-between-bg) text-(--mine-datepicker-pill-between-text) ' + (cell.col === 0 ? 'rounded-s-lg' : cell.col === 6 ? 'rounded-e-lg' : 'rounded-none')
                                    : ''"
                            >
                                <span x-text="cell.day"></span>
                            </span>
                        </button>
                    </template>
                </div>
            </template>
        </div>

        <div class="mt-3 flex justify-center w-full">
            <x-mine.button class="mine-btn-primary h-10!"
                type="button"
                x-on:click="reset()">
                Reset
            </x-mine.button>
        </div>
    </div>
</div>