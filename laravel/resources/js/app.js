import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import * as Jalali from './jalali';

window.Jalali = Jalali;

Alpine.data('mineToast', (position = 'top-right', duration = 4000) => ({
    toasts: [],
    uid: 0,
    progressFrame: null,
    defaultPosition: position,
    defaultDuration: duration,
    positions: {
        'top-right': { class: 'top-4 right-4 items-end', from: 'translateX(120%)' },
        'top-left': { class: 'top-4 left-4 items-start', from: 'translateX(-120%)' },
        'top-center': { class: 'top-4 left-1/2 -translate-x-1/2 items-center', from: 'translateY(-120%)' },
        'bottom-right': { class: 'bottom-4 right-4 items-end', from: 'translateX(120%)' },
        'bottom-left': { class: 'bottom-4 left-4 items-start', from: 'translateX(-120%)' },
        'bottom-center': { class: 'bottom-4 left-1/2 -translate-x-1/2 items-center', from: 'translateY(120%)' },
    },
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
}))

Livewire.start()
