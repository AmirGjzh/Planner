{{-- Project-owned paginator (mine family), reached via ->links('mine.pagination') — never a
     published vendor override. Contract: .ai/rules/mine.md "Pagination". --}}
@php
    // Upstream passes the page list flat for paginators built with onEachSide(); rebuild it
    // into first | ... | slider | ... | last (same reshaping as Livewire's own tailwind view).
    if (count($elements) === 1 && is_array($elements[0])) {
        $total = $paginator->lastPage();
        // Clamp for display: a hand-crafted out-of-range ?page= keeps currentPage() past the
        // last page, and a slider starting beyond $total would render nothing.
        $current = min($paginator->currentPage(), $total);
        $onEachSide = $paginator->onEachSide;

        $start = max(1, $current - $onEachSide);
        $end = min($total, $current + $onEachSide);

        $slider = $paginator->getUrlRange($start, $end);
        $last = $paginator->getUrlRange($total, $total);

        $newElements = [];

        if ($start == 1) {
            $newElements[] = $slider;
        } else {
            $newElements[] = $paginator->getUrlRange(1, 1);
            if ($start > 2) {
                $newElements[] = '...';
            }
            $newElements[] = $slider;
        }

        if ($end < $total - 1) {
            $newElements[] = '...';
        }

        if ($end < $total) {
            $newElements[] = $last;
        }

        $elements = $newElements;
    }

    // Arrows mirror in RTL: the pair swaps with the writing direction.
    [$previousIcon, $nextIcon] = app()->isLocale('en') ? ['Left', 'Right'] : ['Right', 'Left'];

    // Persian digits in fa; English digits pass through untouched.
    $num = fn ($value) => \App\Support\PersianNumber::localize($value);
@endphp

@if ($paginator->hasPages())
    {{-- The animate wrapper lives inside the view (and inside hasPages()) so single-page lists
         render nothing — a caller-side wrapper would leave its margin behind. No wire:key: the
         island morph reuses the node, so the bar reveals once and stays anchored across flips. --}}
    <x-mine.animate class="mt-4 w-full sm:mt-6">
        <nav aria-label="{{ __('Pagination') }}" class="mine-card flex h-16 w-full items-center px-3 md:ps-5">
            {{-- Mobile view --}}
            <div class="flex w-full justify-between md:hidden">
                @if ($paginator->onFirstPage())
                    <button
                        type="button"
                        disabled
                        class="mine-pagination-item flex cursor-default items-center gap-1 py-2 ps-3 pe-4 text-[14px] font-medium text-(--mine-pagination-text-disabled) opacity-70"
                    >
                        <x-mine.icon name="Angles{{ $previousIcon }}2" size="16" weight="filled" />
                        <span @class(['pt-1' => app()->isLocale('en')])>{{ __('Previous') }}</span>
                    </button>
                @else
                    <button
                        type="button"
                        wire:click="previousPage('{{ $paginator->getPageName() }}')"
                        wire:loading.attr="disabled"
                        dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before"
                        class="mine-pagination-item flex cursor-pointer items-center gap-1 py-2 ps-3 pe-4 text-[14px] font-medium"
                    >
                        <x-mine.icon name="Angles{{ $previousIcon }}2" size="16" weight="filled" />
                        <span @class(['pt-1' => app()->isLocale('en')])>{{ __('Previous') }}</span>
                    </button>
                @endif

                @if ($paginator->hasMorePages())
                    <button
                        type="button"
                        wire:click="nextPage('{{ $paginator->getPageName() }}')"
                        wire:loading.attr="disabled"
                        dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before"
                        class="mine-pagination-item flex cursor-pointer items-center gap-1 py-2 ps-4 pe-3 text-[14px] font-medium"
                    >
                        <span @class(['pt-1' => app()->isLocale('en')])>{{ __('Next') }}</span>
                        <x-mine.icon name="Angles{{ $nextIcon }}2" size="16" weight="filled" />
                    </button>
                @else
                    <button
                        type="button"
                        disabled
                        class="mine-pagination-item flex cursor-default items-center gap-1 py-2 ps-4 pe-3 text-[14px] font-medium text-(--mine-pagination-text-disabled) opacity-70"
                    >
                        <span @class(['pt-1' => app()->isLocale('en')])>{{ __('Next') }}</span>
                        <x-mine.icon name="Angles{{ $nextIcon }}2" size="16" weight="filled" />
                    </button>
                @endif
            </div>

            {{-- Desktop view --}}
            <div class="hidden w-full md:flex md:items-center md:justify-between">
                @if ($paginator->firstItem() !== null)
                    <div class="shrink-0">
                        <p class="mine-text-secondary pt-1 text-[14px] font-medium">
                            <span>{{ __('Showing') }}</span>
                            <span class="font-semibold">{{ $num($paginator->firstItem()) }}</span>
                            <span>{{ __('to') }}</span>
                            <span class="font-semibold">{{ $num($paginator->lastItem()) }}</span>
                            <span>{{ __('of') }}</span>
                            <span class="font-semibold">{{ $num($paginator->total()) }}</span>
                        </p>
                    </div>
                @else
                    <div class="shrink-0"></div>
                @endif

                <div class="flex items-center gap-1.5">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <button
                            type="button"
                            disabled
                            aria-label="{{ __('Previous') }}"
                            class="mine-pagination-item inline-flex size-10 cursor-default items-center justify-center !p-0 text-(--mine-pagination-text-disabled) opacity-70"
                        >
                            <x-mine.icon name="Angle{{ $previousIcon }}" size="18" weight="filled" />
                        </button>
                    @else
                        <button
                            type="button"
                            wire:click="previousPage('{{ $paginator->getPageName() }}')"
                            wire:loading.attr="disabled"
                            aria-label="{{ __('Previous') }}"
                            dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after"
                            class="mine-pagination-item inline-flex size-10 cursor-pointer items-center justify-center !p-0"
                        >
                            <x-mine.icon name="Angle{{ $previousIcon }}" size="18" weight="filled" />
                        </button>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" separator --}}
                        @if (is_string($element))
                            <span
                                aria-hidden="true"
                                class="mine-pagination-item inline-flex size-10 cursor-default items-center justify-center !p-0 text-[14px] font-medium text-(--mine-pagination-text-disabled) opacity-70"
                            >{{ $element }}</span>
                        @endif

                        {{-- Array of page links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                    @if ($page == $paginator->currentPage())
                                        <span
                                            aria-current="page"
                                            class="mine-pagination-item mine-btn-primary! inline-flex size-10 cursor-default items-center justify-center border-(--mine-btn-primary-bg) !p-0 text-[14px] font-medium"
                                        >
                                            <span class="pt-1">{{ $num($page) }}</span>
                                        </span>
                                    @else
                                        <button
                                            type="button"
                                            wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                            wire:loading.attr="disabled"
                                            class="mine-pagination-item inline-flex size-10 cursor-pointer items-center justify-center !p-0 text-[14px] font-medium"
                                        >
                                            <span class="pt-1">{{ $num($page) }}</span>
                                        </button>
                                    @endif
                                </span>
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <button
                            type="button"
                            wire:click="nextPage('{{ $paginator->getPageName() }}')"
                            wire:loading.attr="disabled"
                            aria-label="{{ __('Next') }}"
                            dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after"
                            class="mine-pagination-item inline-flex size-10 cursor-pointer items-center justify-center !p-0"
                        >
                            <x-mine.icon name="Angle{{ $nextIcon }}" size="18" weight="filled" />
                        </button>
                    @else
                        <button
                            type="button"
                            disabled
                            aria-label="{{ __('Next') }}"
                            class="mine-pagination-item inline-flex size-10 cursor-default items-center justify-center !p-0 text-(--mine-pagination-text-disabled) opacity-70"
                        >
                            <x-mine.icon name="Angle{{ $nextIcon }}" size="18" weight="filled" />
                        </button>
                    @endif
                </div>
            </div>
        </nav>
    </x-mine.animate>
@endif
