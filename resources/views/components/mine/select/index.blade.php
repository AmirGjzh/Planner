@props([
    'name' => $attributes->whereStartsWith('wire:model')->first() ?? $attributes->whereStartsWith('x-model')->first(),
    'label' => null,
    'placeholder' => 'Select...',
    'searchable' => false,
    'disabled' => false,
    'invalid' => false,
    'position' => 'bottom-start',
    'height' => 'h-11',
])

@php
    $positionClasses = str_starts_with($position, 'top')
        ? 'bottom-full mb-1.5 left-0 right-0'
        : 'top-full mt-1.5 left-0 right-0';

    if ($name && $errors->has($name)) {
        $invalid = true;
    }
@endphp

<div
    x-data="{
        state: null,
        selectedLabel: @js($placeholder),
        isOpen: false,
        search: '',
        resultsCount: 0,

        init() {
            @if($name)
                if (typeof $wire !== 'undefined') {
                    this.state = this.normalize($wire.get(@js($name)))
                    $wire.$watch(@js($name), (value) => {
                        this.state = this.normalize(value)
                        this.syncLabel()
                    })
                }
            @endif

            if (this.hasSelection) {
                this.syncLabel()
            }

            this.$watch('state', () => this.syncLabel())

            this.$watch('search', () => {
                this.$nextTick(() => {
                    this.resultsCount = Array.from(this.$root.querySelectorAll('[role=option]'))
                        .filter(el => el.offsetParent !== null).length
                })
            })
        },

        normalize(value) {
            return value === null || value === undefined || value === '' ? value : String(value)
        },

        syncLabel() {
            this.$nextTick(() => {
                if (this.hasSelection) {
                    const options = this.$root.querySelectorAll('[role=option]')
                    const matched = Array.from(options).find(el => el.dataset.value === this.state)
                    if (matched) {
                        this.selectedLabel = matched.dataset.label
                    }
                } else {
                    this.selectedLabel = @js($placeholder)
                }
            })
        },

        get hasSelection() {
            return this.state !== null && this.state !== undefined && this.state !== ''
        },

        toggle() {
            this.isOpen ? this.close() : this.open()
        },

        open() {
            if (@js($disabled)) return
            this.isOpen = true
            this.search = ''
            this.focusedIndex = -1
            this.$nextTick(() => {
                const searchInput = this.$root.querySelector('[data-select-search]')
                if (searchInput) {
                    searchInput.focus()
                } else {
                    this.focusFirst()
                }
            })
        },

        close() {
            this.isOpen = false
            this.search = ''
            this.focusedIndex = -1
        },

        select(value, label) {
            this.state = value
            this.selectedLabel = label
            const hidden = this.$root.querySelector('input[type=hidden]')
            if (hidden) {
                hidden.value = value ?? ''
                hidden.dispatchEvent(new Event('input', { bubbles: true }))
            }
            this.close()
        },

        handleClickAway(target) {
            const trigger = this.$root.querySelector('[data-select-trigger]')
            if (trigger && trigger.contains(target)) return
            this.close()
        },

        get visibleOptions() {
            return Array.from(this.$root.querySelectorAll('[role=option]'))
                .filter(el => el.offsetParent !== null)
        },

        focusFirst() {
            const items = this.visibleOptions
            if (items.length > 0) {
                items[0].focus()
                this.focusedIndex = 0
            }
        },

        focusLast() {
            const items = this.visibleOptions
            if (items.length > 0) {
                items[items.length - 1].focus()
                this.focusedIndex = items.length - 1
            }
        },

        focusNext() {
            const items = this.visibleOptions
            const current = document.activeElement
            const idx = items.indexOf(current)
            const next = items[Math.min(idx + 1, items.length - 1)]
            if (next) {
                next.focus()
                this.focusedIndex = Math.min(idx + 1, items.length - 1)
            }
        },

        focusPrev() {
            const items = this.visibleOptions
            const current = document.activeElement
            const idx = items.indexOf(current)
            const prev = items[Math.max(idx - 1, 0)]
            if (prev) {
                prev.focus()
                this.focusedIndex = Math.max(idx - 1, 0)
            }
        },

        handleOptionKeydown(event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault()
                const option = event.currentTarget
                const value = option.dataset.value
                const label = option.dataset.label
                if (value !== undefined) {
                    this.select(value, label)
                }
            }
            if (event.key === 'Escape') {
                event.preventDefault()
                this.close()
            }
        },
    }"
    x-on:keydown.down.prevent="focusNext()"
    x-on:keydown.up.prevent="focusPrev()"
    x-on:keydown.home.prevent="focusFirst()"
    x-on:keydown.end.prevent="focusLast()"
    x-on:keydown.escape.prevent="close()"
    @if($disabled) aria-disabled="true" @endif
    @if($invalid) aria-invalid="true" @endif
    role="listbox"
    {{ $attributes->class(['relative w-full']) }}
>
    @if ($label)
        <label class="mb-2 block text-sm font-medium mine-text-primary">
            {{ $label }}
        </label>
    @endif

    @if ($name)
        <input type="hidden" name="{{ $name }}" wire:model.defer="{{ $name }}" x-bind:value="state" />
    @endif

    <div class="relative">
        <x-mine.select.trigger />

        <div
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            x-on:click.away="handleClickAway($event.target)"
            class="absolute {{ $positionClasses }} z-50 bg-(--mine-input-bg) rounded-xl border-2 border-(--mine-input-border) shadow-lg"
        >
            @if ($searchable)
                <div class="flex items-center gap-2 px-4 py-2 border-b border-(--mine-input-border)">
                    <x-mine.icon name="magnifying-glass" class="size-5 text-(--mine-input-icon) shrink-0" variant="micro" />
                    <input
                        x-model="search"
                        data-select-search
                        type="text"
                        placeholder="{{ __('Search...') }}"
                        class="w-full h-8 bg-transparent text-sm mine-text-primary placeholder:text-(--mine-input-placeholder) focus:outline-none focus-visible:outline-none"
                    />
                </div>
            @endif

            <ul
                class="max-h-60 overflow-y-auto mine-scrollbar p-1"
                role="listbox"
            >
                {{ $slot }}

                <li
                    x-show="isOpen && search !== '' && resultsCount === 0"
                    class="flex items-center justify-center h-14 text-sm mine-text-secondary"
                >
                    {{ __('No results found') }}
                </li>
            </ul>
        </div>
    </div>

    @error($name)
        <p class="mt-2 text-sm font-medium mine-text-error">{{ $message }}</p>
    @enderror
</div>
