@props([
    'label',
    'searchable' => false,
])

<div
    x-data="{ open: false, search: '' }"
    @click.away="
        open = false;
        search = '';
    "
    class="relative inline-flex overflow-visible"
>
    <button
        type="button"
        @click="
            open = ! open;
            if (! open) search = '';
            if (open) $nextTick(() => $refs.search && $refs.search.focus());
        "
        :data-open="open"
        data-datepicker-select="{{ $label }}"
        class="flex cursor-pointer items-center gap-1 rounded-xl border-2 border-(--mine-input-border)/0 bg-(--mine-input-bg) px-3 py-1.5 text-sm font-medium text-[var(--mine-text-primary)]/80 transition-[border-color,box-shadow] duration-200 focus-visible:ring-2 focus-visible:ring-(--mine-input-ring-focus) focus-visible:outline-none data-open:border-(--mine-input-border-focus) data-open:ring-2 data-open:ring-(--mine-input-ring-focus)"
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
        class="absolute top-full left-1/2 z-50 mt-1 w-max -translate-x-1/2 rounded-xl border-2 border-(--mine-input-border) bg-(--mine-input-bg) shadow-lg"
    >
        @if ($searchable)
            <div class="flex items-center gap-2 border-b border-(--mine-input-border) px-4 py-2">
                <x-mine.icon name="Magnifier" weight="filled" size="20" class="shrink-0 text-(--mine-input-icon)" />
                <input
                    x-ref="search"
                    x-model="search"
                    type="text"
                    placeholder="{{ __('Search...') }}"
                    class="h-8 w-full bg-transparent text-sm font-medium text-[var(--mine-text-primary)]/80 placeholder:font-medium placeholder:text-(--mine-input-placeholder) focus:outline-none focus-visible:outline-none"
                />
            </div>
        @endif

        <div class="mine-scrollbar max-h-60 overflow-y-auto p-1">{{ $slot }}</div>
    </div>
</div>
