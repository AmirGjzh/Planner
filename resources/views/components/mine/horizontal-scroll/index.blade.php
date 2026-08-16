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
                        this.scroller.scrollLeft = today.offsetLeft - (this.scroller.clientWidth / 2) + (today.clientWidth / 2);
                        this.update();
                    }
                });
            @endif
        },

        destroy() {
            this.observer?.disconnect();
        },

        update() {
            if (! this.scroller) return;

            this.canPrev = this.scroller.scrollLeft > 4;
            this.canNext = this.scroller.scrollLeft < this.scroller.scrollWidth - this.scroller.clientWidth - 4;
        },

        scrollToStart() {
            this.scroller.scrollTo({ left: 0, behavior: 'smooth' });
        },

        scrollToEnd() {
            this.scroller.scrollTo({ left: this.scroller.scrollWidth, behavior: 'smooth' });
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
            class="absolute left-2 top-1/2 z-10 -translate-y-1/2 flex size-9 items-center justify-center rounded-full border border-(--mine-card-border) bg-(--mine-dropdown-bg) text-(--mine-text-secondary) shadow-md transition-colors duration-200 hover:bg-(--mine-card-bg-hover) hover:text-(--mine-text-primary)"
            aria-label="Scroll to the beginning"
        >
            <x-mine.icon name="chevron-left" variant="mini" class="size-5" />
        </button>
    </template>

    <template x-if="canNext">
        <button
            type="button"
            x-on:click="scrollToEnd()"
            x-transition.opacity.duration.150ms
            class="absolute right-2 top-1/2 z-10 -translate-y-1/2 flex size-9 items-center justify-center rounded-full border border-(--mine-card-border) bg-(--mine-dropdown-bg) text-(--mine-text-secondary) shadow-md transition-colors duration-200 hover:bg-(--mine-card-bg-hover) hover:text-(--mine-text-primary)"
            aria-label="Scroll to the end"
        >
            <x-mine.icon name="chevron-right" variant="mini" class="size-5" />
        </button>
    </template>
</div>
