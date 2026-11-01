@props([
    'id' => null,
    'width' => 'lg',
    'position' => 'center',
    'backdrop' => 'dark',
    'closeByClickingAway' => true,
    'closeByEscaping' => true,
    'openEventName' => 'open-modal',
    'closeEventName' => 'close-modal',
    'labelledBy' => null,
])

@php
    $modalId = $id ?? 'modal-'.uniqid();

    $widthClass = match ($width) {
        'xs' => 'max-w-xs',
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
        default => 'max-w-lg',
    };

    $positionClass = match ($position) {
        'top' => 'items-start pt-16',
        default => 'items-center',
    };
@endphp

<div
    x-data="{
        isOpen: false,
        closeByClickingAway: @js($closeByClickingAway),
        closeByEscaping: @js($closeByEscaping),
        modalId: @js($modalId),
        closeEventName: @js($closeEventName),
        openEventName: @js($openEventName),
        returnFocus: null,

        init() {
            window.addEventListener(this.closeEventName, (e) => {
                if (! e.detail?.id || e.detail.id === this.modalId) {
                    this.close()
                }
            })

            window.addEventListener(this.openEventName, (e) => {
                if (e.detail?.id === this.modalId) {
                    this.open()
                }
            })
        },

        open() {
            const active = document.activeElement
            this.returnFocus = active && active !== document.body ? active : null
            this.isOpen = true
            document.body.style.overflow = 'hidden'
            this.$nextTick(() => document.getElementById(this.modalId + '-dialog')?.focus({ preventScroll: true }))
        },

        close() {
            this.isOpen = false
            document.body.style.overflow = ''
            const target = this.returnFocus
            this.returnFocus = null
            if (target && target.isConnected) {
                target.focus({ preventScroll: true })
            }
        },

        handleBackdropClick(event) {
            if (this.closeByClickingAway && event.target === event.currentTarget) {
                this.close()
            }
        },

        // Escape keeps its original semantics; Tab is trapped inside the dialog
        // while open (hand-rolled — no Alpine trap plugin ships with Livewire).
        handleKeydown(event) {
            if (event.key === 'Escape' && this.closeByEscaping) {
                this.close()

                return
            }

            if (event.key !== 'Tab' || ! this.isOpen) {
                return
            }

            const dialog = document.getElementById(this.modalId + '-dialog')
            if (! dialog) {
                return
            }

            const focusables = Array.from(dialog.querySelectorAll(
                `a[href], button:not([disabled]), input:not([disabled]):not([type='hidden']), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex='-1'])`
            )).filter((el) => el.getClientRects().length > 0)

            if (focusables.length === 0) {
                event.preventDefault()

                return
            }

            const first = focusables[0]
            const last = focusables[focusables.length - 1]
            const active = document.activeElement
            const activeIsInside = active && focusables.includes(active)

            if (event.shiftKey) {
                if (! activeIsInside || active === first) {
                    event.preventDefault()
                    last.focus()
                }
            } else if (! activeIsInside || active === last) {
                event.preventDefault()
                first.focus()
            }
        },
    }"
    x-on:keydown.window="handleKeydown($event)"
    {{ $attributes->merge(['class' => 'inline-block overscroll-contain']) }}
>
    <template x-teleport="body">
        <div
            id="{{ $modalId }}-dialog"
            tabindex="-1"
            x-show="isOpen"
            class="fixed inset-0 z-100 overflow-y-auto focus:outline-none"
            role="dialog"
            aria-modal="true"
            @if ($labelledBy) aria-labelledby="{{ $labelledBy }}" @endif
        >
            <div
                x-show="isOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @class([
                    'fixed inset-0',
                    'bg-black/50' => $backdrop === 'dark',
                    'bg-transparent' => $backdrop === 'transparent',
                ])
                @click="handleBackdropClick($event)"
            ></div>

            <div class="relative flex min-h-full {{ $positionClass }} p-4 z-10 justify-center">
                <div
                    x-show="isOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-97"
                    class="relative w-full {{ $widthClass }} mine-card bg-(--mine-modal-bg)"
                >
                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>
</div>
