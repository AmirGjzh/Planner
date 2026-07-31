@props([
    'triggerMode' => 'click',
    'group' => null,
])

<div
    x-data="{
        open: false,
        triggerMode: @js($triggerMode),
        group: @js($group),
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
    {{ $attributes->merge(['class' => 'relative']) }}
>
    {{ $slot }}
</div>
