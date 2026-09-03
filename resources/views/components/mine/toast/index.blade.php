@props([
    'position' => 'top-right',
    'duration' => 4000,
])

@php
    $positions = [
        'top-right' => ['class' => 'top-4 right-4 items-end', 'from' => 'translateX(120%)'],
        'top-left' => ['class' => 'top-4 left-4 items-start', 'from' => 'translateX(-120%)'],
        'top-center' => ['class' => 'top-4 left-1/2 -translate-x-1/2 items-center', 'from' => 'translateY(-120%)'],
        'bottom-right' => ['class' => 'bottom-4 right-4 items-end', 'from' => 'translateX(120%)'],
        'bottom-left' => ['class' => 'bottom-4 left-4 items-start', 'from' => 'translateX(-120%)'],
        'bottom-center' => ['class' => 'bottom-4 left-1/2 -translate-x-1/2 items-center', 'from' => 'translateY(120%)'],
    ];
@endphp

<div
    x-data="{
        toasts: [],
        uid: 0,
        progressFrame: null,
        defaultPosition: @js($position),
        defaultDuration: @js($duration),
        positions: @js($positions),
        variants: {
            danger: {
                container: 'border-[var(--mine-alert-danger-border)] bg-[var(--mine-alert-danger-bg)]',
                title: 'text-[var(--mine-alert-danger-title)]',
                icon: 'text-[var(--mine-alert-danger-icon)]',
            },
            success: {
                container: 'border-[var(--mine-alert-success-border)] bg-[var(--mine-alert-success-bg)]',
                title: 'text-[var(--mine-alert-success-title)]',
                icon: 'text-[var(--mine-alert-success-icon)]',
            },
            warning: {
                container: 'border-[var(--mine-alert-warning-border)] bg-[var(--mine-alert-warning-bg)]',
                title: 'text-[var(--mine-alert-warning-title)]',
                icon: 'text-[var(--mine-alert-warning-icon)]',
            },
            info: {
                container: 'border-[var(--mine-alert-info-border)] bg-[var(--mine-alert-info-bg)]',
                title: 'text-[var(--mine-alert-info-title)]',
                icon: 'text-[var(--mine-alert-info-icon)]',
            },
        },
        addToast({ title, variant = 'info', position = null, duration = null }) {
            if (! title) return

            const toast = {
                id: ++this.uid,
                title,
                variant: this.variants[variant] ? variant : 'info',
                position: this.resolvePosition(position),
                duration: duration && duration > 0 ? duration : this.defaultDuration,
                remaining: duration && duration > 0 ? duration : this.defaultDuration,
                startedAt: null,
                timer: null,
                progress: 1,
                leaving: false,
            }

            this.toasts.push(toast)
            this.startTimer(toast)
            if (! this.progressFrame) {
                this.progressFrame = requestAnimationFrame(() => this.tickProgress())
            }
        },
        resolvePosition(position) {
            if (! position || typeof position !== 'object') {
                return this.positions[position] ? position : this.defaultPosition
            }

            for (const [query, pos] of Object.entries(position)) {
                if (query !== 'default' && window.matchMedia(query).matches && this.positions[pos]) {
                    return pos
                }
            }

            const fallback = position.default ?? this.defaultPosition

            return this.positions[fallback] ? fallback : this.defaultPosition
        },
        tickProgress() {
            this.progressFrame = null

            if (this.toasts.length === 0) return

            for (const toast of this.toasts) {
                if (toast.timer) {
                    toast.progress = Math.max(0, (toast.remaining - (performance.now() - toast.startedAt)) / toast.duration)
                }
            }

            this.progressFrame = requestAnimationFrame(() => this.tickProgress())
        },
        removeToast(id) {
            const toast = this.toasts.find((item) => item.id === id)
            if (! toast || toast.leaving) return

            toast.leaving = true
            clearTimeout(toast.timer)
            setTimeout(() => {
                this.toasts = this.toasts.filter((item) => item.id !== id)
            }, 220)
        },
        startTimer(toast) {
            toast.startedAt = performance.now()
            toast.timer = setTimeout(() => this.removeToast(toast.id), toast.remaining)
        },
        pauseToast(toast) {
            if (! toast.timer) return
            clearTimeout(toast.timer)
            toast.timer = null
            toast.remaining -= performance.now() - toast.startedAt
            toast.progress = Math.max(0, toast.remaining / toast.duration)
        },
        resumeToast(toast) {
            if (toast.timer) return
            if (toast.remaining <= 0) {
                this.removeToast(toast.id)
                return
            }
            this.startTimer(toast)
        },
        variantClass(toast) {
            return this.variants[toast.variant] ?? this.variants.info
        },
        toastsFor(position) {
            return this.toasts.filter((toast) => toast.position === position)
        },
    }"
    x-on:toast.window="addToast($event.detail ?? {})"
    class="fixed inset-0 z-100 pointer-events-none"
    aria-live="polite"
>
    <template x-for="(position, key) in positions" :key="key">
        <div
            class="absolute flex flex-col gap-2  p-2"
            :class="position.class"
            :style="{ '--mine-toast-from': position.from }"
            x-show="toastsFor(key).length > 0"
        >
            <template x-for="toast in toastsFor(key)" :key="toast.id">
                <div
                    class="pointer-events-auto relative w-max min-w-80 max-w-[calc(100vw-2rem)] flex items-center gap-3 overflow-hidden rounded-xl border-2 p-3.5"
                    :class="[variantClass(toast).container, toast.leaving ? 'mine-toast-leaving' : 'mine-toast-enter']"
                    @mouseenter="pauseToast(toast)"
                    @mouseleave="resumeToast(toast)"
                >
                    <div
                        class="mine-toast-progress pointer-events-none absolute inset-0 origin-left bg-(--mine-toast-progress-bg)"
                        :style="{ transform: 'scaleX(' + toast.progress + ')' }"
                    ></div>

                    <div class="relative size-6 shrink-0">
                        <div x-show="toast.variant === 'danger'" class="absolute inset-0 text-[var(--mine-alert-danger-icon)]">
                            <x-mine.icon name="ShieldAlert" size="24" />
                        </div>
                        <div x-show="toast.variant === 'success'" class="absolute inset-0 text-[var(--mine-alert-success-icon)]">
                            <x-mine.icon name="CheckCircle" size="24" />
                        </div>
                        <div x-show="toast.variant === 'warning'" class="absolute inset-0 text-[var(--mine-alert-warning-icon)]">
                            <x-mine.icon name="AlertTriangle" size="24" />
                        </div>
                        <div x-show="toast.variant === 'info'" class="absolute inset-0 text-[var(--mine-alert-info-icon)]">
                            <x-mine.icon name="AlertCircle" size="24" />
                        </div>
                    </div>

                    <h3
                        class="min-w-0 flex-1 text-sm font-semibold mt-1"
                        :class="variantClass(toast).title"
                        x-text="toast.title"
                    ></h3>

                    <button
                        type="button"
                        class="shrink-0 rounded-lg p-1 mine-text-secondary transition-colors duration-200 hover:cursor-pointer focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-(--mine-input-ring-focus)"
                        :class="variantClass(toast).icon"
                        @click="removeToast(toast.id)"
                        aria-label="Close notification"
                    >
                        <x-mine.icon name="Xmark" size="14" weight="filled" />
                    </button>
                </div>
            </template>
        </div>
    </template>
</div>