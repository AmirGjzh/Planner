import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import * as Jalali from './jalali';

window.Jalali = Jalali;

Alpine.data('mineToast', (position = 'top-right', duration = 4000) => ({
    toasts: [],
    uid: 0,
    progressFrame: null,
    defaultPosition: position,
    defaultDuration: duration,
    storageKey: 'mine-toasts',
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
    init() {
        this.restoreToasts()
    },
    addToast({ title, variant = 'info', position = null, duration = null }) {
        if (! title) return

        const ms = duration && duration > 0 ? duration : this.defaultDuration

        const toast = {
            id: ++this.uid,
            title,
            variant: this.variants[variant] ? variant : 'info',
            position: this.resolvePosition(position),
            duration: ms,
            remaining: ms,
            expiresAt: Date.now() + ms,
            startedAt: null,
            timer: null,
            progress: 1,
            leaving: false,
        }

        this.toasts.push(toast)
        this.persistToasts()
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
                toast.progress = Math.min(1, Math.max(0, (toast.remaining - (performance.now() - toast.startedAt)) / toast.duration))
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
            this.persistToasts()
        }, 220)
    },
    persistToasts() {
        const now = Date.now()
        const alive = this.toasts
            .filter((toast) => ! toast.leaving && toast.expiresAt > now)
            .slice(-5)
            .map(({ title, variant, position, duration, expiresAt }) => ({ title, variant, position, duration, expiresAt }))

        try {
            if (alive.length === 0) {
                sessionStorage.removeItem(this.storageKey)
            } else {
                sessionStorage.setItem(this.storageKey, JSON.stringify(alive))
            }
        } catch (error) {
            // Storage unavailable — toasts simply do not cross navigations.
        }
    },
    restoreToasts() {
        let saved = []

        try {
            saved = JSON.parse(sessionStorage.getItem(this.storageKey)) || []
        } catch (error) {
            saved = []
        }

        if (! Array.isArray(saved) || saved.length === 0) {
            // Re-persist even on an empty/corrupt read so a poisoned key is
            // pruned away instead of lingering until the next toast fires.
            this.persistToasts()
            return
        }

        const now = Date.now()

        for (const item of saved) {
            if (! item || typeof item.title !== 'string' || item.title === '') continue
            if (typeof item.expiresAt !== 'number' || item.expiresAt <= now) continue

            const remaining = item.expiresAt - now
            const duration = typeof item.duration === 'number' && item.duration > 0 ? item.duration : this.defaultDuration

            const toast = {
                id: ++this.uid,
                title: item.title,
                variant: this.variants[item.variant] ? item.variant : 'info',
                position: this.resolvePosition(item.position),
                duration,
                remaining,
                expiresAt: item.expiresAt,
                startedAt: null,
                timer: null,
                progress: Math.min(1, remaining / duration),
                leaving: false,
            }

            this.toasts.push(toast)
            this.startTimer(toast)
        }

        this.persistToasts()

        if (! this.progressFrame) {
            this.progressFrame = requestAnimationFrame(() => this.tickProgress())
        }
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
        // A hover pause stops the wall-clock deadline too: re-arm it from the
        // frozen remaining time so storage and screen agree after a reload.
        toast.expiresAt = Date.now() + toast.remaining
        this.persistToasts()
        this.startTimer(toast)
    },
    variantClass(toast) {
        return this.variants[toast.variant] ?? this.variants.info
    },
    toastsFor(position) {
        return this.toasts.filter((toast) => toast.position === position)
    },
}))

Alpine.data('mineLogout', ({ action, home, csrf, failed }) => ({
    busy: false,
    logout() {
        if (this.busy) return
        this.busy = true
        fetch(action, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf } })
            .then((response) => {
                if (! response.ok) throw new Error('Logout request failed')
                window.location.assign(home)
            })
            .catch(() => {
                // Non-ok (rate block, expired CSRF) or network failure — the
                // session is still alive, so say so instead of failing silently.
                this.busy = false
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { title: failed, variant: 'danger', position: 'bottom-center' },
                }))
            })
    },
}))

// Search matching across scripts: Persian (۰-۹) and Arabic-Indic (٠-٩) digits
// normalize to Western digits on both sides of a comparison, so a query typed
// in either numeral system finds the same option. Letters are left alone.
// The magic callback must return the callable (it runs as a property getter).
Alpine.magic('digits', (el) => (value) =>
    String(value ?? '').replace(/[۰-۹٠-٩]/g, (digit) => {
        const code = digit.charCodeAt(0)
        return String(code - (code >= 0x06f0 ? 0x06f0 : 0x0660))
    })
)

Livewire.start()
