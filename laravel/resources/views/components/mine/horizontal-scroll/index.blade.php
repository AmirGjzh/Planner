@props([
    'todayIndex' => null,
    'class' => '',
    'scrollerClass' => '',
])

<div
    x-data="{
        scroller: null,
        canPrev: false,
        canNext: false,
        observer: null,

        init() {
            this.scroller = this.$refs.scroller;
            this.update();

            if (typeof ResizeObserver !== 'undefined') {
                this.observer = new ResizeObserver(() => this.update());
                this.observer.observe(this.scroller);
            }

            @if ($todayIndex !== null && $todayIndex !== false)
                this.$nextTick(() => {
                    const cells = this.scroller.querySelectorAll('[data-day]');
                    const today = cells[{{ $todayIndex }}];

                    if (today) {
                        const scrollerRect = this.scroller.getBoundingClientRect();
                        const cellRect = today.getBoundingClientRect();
                        const center = this.scroller.scrollLeft
                            + (cellRect.left - scrollerRect.left)
                            + (cellRect.width / 2)
                            - (this.scroller.clientWidth / 2);
                        this.scroller.scrollLeft = center;
                        this.update();
                    }
                });
            @endif
        },

        destroy() {
            this.observer?.disconnect();
        },

        isRtl() {
            return window.getComputedStyle(this.scroller).direction === 'rtl';
        },

        update() {
            if (! this.scroller) return;

            const max = this.scroller.scrollWidth - this.scroller.clientWidth;
            const position = this.isRtl() ? -this.scroller.scrollLeft : this.scroller.scrollLeft;

            this.canPrev = position > 4;
            this.canNext = position < max - 4;
        },

        scrollToStart() {
            this.scroller.scrollTo({ left: 0, behavior: 'smooth' });
        },

        scrollToEnd() {
            const left = this.isRtl() ? -this.scroller.scrollWidth : this.scroller.scrollWidth;

            this.scroller.scrollTo({ left, behavior: 'smooth' });
        },
    }"
    class="relative w-full {{ $class }}"
>
    <div
        x-ref="scroller"
        x-on:scroll="update()"
        class="overflow-x-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden {{ $scrollerClass }}"
    >
        {{ $slot }}
    </div>

    <template x-if="canPrev">
        <button
            type="button"
            x-on:click="scrollToStart()"
            x-transition.opacity.duration.150ms
            class="absolute start-2 top-1/2 z-10 flex size-9 -translate-y-1/2 items-center justify-center rounded-full border border-(--mine-card-border) bg-(--mine-dropdown-bg) text-(--mine-text-secondary) shadow-md transition-colors duration-200 hover:bg-(--mine-card-bg-hover) hover:text-(--mine-text-primary)"
            aria-label="Scroll to the beginning"
        >
            <x-mine.icon name="Angle{{ app()->isLocale('fa') ? 'Right' : 'Left' }}" variant="mini" class="size-5" />
        </button>
    </template>

    <template x-if="canNext">
        <button
            type="button"
            x-on:click="scrollToEnd()"
            x-transition.opacity.duration.150ms
            class="absolute end-2 top-1/2 z-10 flex size-9 -translate-y-1/2 items-center justify-center rounded-full border border-(--mine-card-border) bg-(--mine-dropdown-bg) text-(--mine-text-secondary) shadow-md transition-colors duration-200 hover:bg-(--mine-card-bg-hover) hover:text-(--mine-text-primary)"
            aria-label="Scroll to the end"
        >
            <x-mine.icon name="Angle{{ app()->isLocale('fa') ? 'Left' : 'Right' }}" variant="mini" class="size-5" />
        </button>
    </template>
</div>
