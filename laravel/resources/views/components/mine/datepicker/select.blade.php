@props([
    'label',
])

<div x-data="{ open: false }" @click.away="open = false" class="relative inline-flex overflow-visible">
    <button
        type="button"
        @click="open = ! open"
        :data-open="open"
        class="mine-text-primary flex cursor-pointer items-center gap-1 rounded-xl border-2 border-(--mine-input-border)/0 bg-(--mine-input-bg) px-3 py-1.5 text-sm font-medium transition-all duration-200 focus-visible:ring-2 focus-visible:ring-(--mine-input-ring-focus) focus-visible:outline-none data-open:border-(--mine-input-border-focus) data-open:ring-2 data-open:ring-(--mine-input-ring-focus)"
    >
        <span x-text="{{ $label }}"></span>
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="mine-scrollbar absolute top-full left-1/2 z-50 mt-1 max-h-60 w-max -translate-x-1/2 overflow-y-auto rounded-xl border-2 border-(--mine-input-border) bg-(--mine-input-bg) p-1 shadow-lg"
    >
        {{ $slot }}
    </div>
</div>
