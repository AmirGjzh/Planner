@props([
    'position' => 'top-right',
    'duration' => 4000,
])

<div
    x-data="mineToast(@js($position), @js($duration))"
    x-on:toast.window="addToast($event.detail ?? {})"
    class="pointer-events-none fixed inset-0 z-100"
    aria-live="polite"
>
    <template x-for="(position, key) in positions" :key="key">
        <div
            class="absolute flex flex-col gap-2 p-2"
            :class="position.class"
            :style="{ '--mine-toast-from': position.from }"
            x-show="toastsFor(key).length > 0"
        >
            <template x-for="toast in toastsFor(key)" :key="toast.id">
                <div
                    class="pointer-events-auto relative flex w-max max-w-[calc(100vw-2rem)] min-w-80 items-center gap-3 overflow-hidden rounded-xl border-2 p-3.5"
                    :class="[variantClass(toast).container, toast.leaving ? 'mine-toast-leaving' : 'mine-toast-enter']"
                    @mouseenter="pauseToast(toast)"
                    @mouseleave="resumeToast(toast)"
                >
                    <div
                        class="mine-toast-progress pointer-events-none absolute inset-0 origin-left bg-(--mine-toast-progress-bg)"
                        :style="{ transform: 'scaleX(' + toast.progress + ')' }"
                    ></div>

                    <div class="relative size-6 shrink-0">
                        <div
                            x-show="toast.variant === 'danger'"
                            class="absolute inset-0 text-[var(--mine-alert-danger-icon)]"
                        >
                            <x-mine.icon name="ShieldAlert" size="24" />
                        </div>
                        <div
                            x-show="toast.variant === 'success'"
                            class="absolute inset-0 text-[var(--mine-alert-success-icon)]"
                        >
                            <x-mine.icon name="CheckCircle" size="24" />
                        </div>
                        <div
                            x-show="toast.variant === 'warning'"
                            class="absolute inset-0 text-[var(--mine-alert-warning-icon)]"
                        >
                            <x-mine.icon name="AlertTriangle" size="24" />
                        </div>
                        <div
                            x-show="toast.variant === 'info'"
                            class="absolute inset-0 text-[var(--mine-alert-info-icon)]"
                        >
                            <x-mine.icon name="AlertCircle" size="24" />
                        </div>
                    </div>

                    <h3
                        class="mt-1 min-w-0 flex-1 text-sm font-semibold"
                        :class="variantClass(toast).title"
                        x-text="toast.title"
                    ></h3>

                    <button
                        type="button"
                        class="mine-text-secondary shrink-0 rounded-lg p-1 transition-colors duration-200 hover:cursor-pointer focus-visible:ring-4 focus-visible:ring-(--mine-input-ring-focus) focus-visible:outline-none"
                        :class="variantClass(toast).icon"
                        @click="removeToast(toast.id)"
                        aria-label="{{ __('Close notification') }}"
                    >
                        <x-mine.icon name="Xmark" size="14" weight="filled" />
                    </button>
                </div>
            </template>
        </div>
    </template>
</div>
