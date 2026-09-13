@props([
    'id' => null,
    'width' => 'lg',
    'position' => 'center',
    'backdrop' => 'dark',
    'closeByClickingAway' => true,
    'closeByEscaping' => true,
    'openEventName' => 'open-modal',
    'closeEventName' => 'close-modal',
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
            this.isOpen = true
            document.body.style.overflow = 'hidden'
        },

        close() {
            this.isOpen = false
            document.body.style.overflow = ''
        },

        handleBackdropClick(event) {
            if (this.closeByClickingAway && event.target === event.currentTarget) {
                this.close()
            }
        },

        handleEscapeKey(event) {
            if (event.key === 'Escape' && this.closeByEscaping) {
                this.close()
            }
        },
    }"
    x-on:keydown.window="handleEscapeKey($event)"
    {{ $attributes->merge(['class' => 'inline-block overscroll-contain']) }}
>
    <template x-teleport="body">
        <div
            x-show="isOpen"
            class="fixed inset-0 z-100 overflow-y-auto"
            role="dialog"
            aria-modal="true"
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
