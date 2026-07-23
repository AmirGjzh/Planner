@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';

// Rebuild flat page lists into first | ... | slider | ... | last format
if (count($elements) === 1 && is_array($elements[0])) {
    $total = $paginator->lastPage();
    $current = $paginator->currentPage();
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
        if ($start > 2) $newElements[] = '...';
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
@endphp

<div class="w-full">
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="w-full mine-card h-18 flex items-center px-4 sm:px-6">
            {{-- Mobile view --}}
            <div class="w-full flex justify-between sm:hidden">
                @if ($paginator->onFirstPage())
                    <span class="pagination-item pr-4 pl-3 py-2 opacity-50 cursor-default flex gap-1 items-center">
                        <x-mine.icon name="chevron-double-left" variant="micro" class="size-4" />
                        Previous
                    </span>
                @else
                    <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" class="pagination-item pr-4 pl-3 py-2 flex gap-1 items-center cursor-pointer">
                        <x-mine.icon name="chevron-double-left" variant="micro" class="size-4" />
                        Previous
                    </button>
                @endif

                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" class="pagination-item pl-4 pr-3 py-2 flex gap-1 items-center cursor-pointer">
                        Next
                        <x-mine.icon name="chevron-double-right" variant="micro" class="size-4" />
                    </button>
                @else
                    <span class="pagination-item pl-4 pr-3 py-2 flex gap-1 items-center opacity-50 cursor-default">
                        Next
                        <x-mine.icon name="chevron-double-right" variant="micro" class="size-4" />
                    </span>
                @endif
            </div>

            {{-- Desktop view --}}
            <div class="hidden sm:flex sm:items-center sm:justify-between w-full gap-4">
                <div class="shrink-0">
                    <p class="text-sm mine-text-secondary font-medium">
                        <span>{!! __('Showing') !!}</span>
                        <span class="font-medium mine-text-primary">{{ $paginator->firstItem() }}</span>
                        <span>{!! __('to') !!}</span>
                        <span class="font-medium mine-text-primary">{{ $paginator->lastItem() }}</span>
                        <span>{!! __('of') !!}</span>
                        <span class="font-medium mine-text-primary">{{ $paginator->total() }}</span>
                        <span>{!! __('results') !!}</span>
                    </p>
                </div>

                <div class="flex items-center gap-1.5">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="pagination-item size-10 !p-0 inline-flex items-center justify-center opacity-50 cursor-default" aria-hidden="true">
                                <x-mine.icon name="chevron-left" variant="micro" />
                            </span>
                        </span>
                    @else
                        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" class="pagination-item size-10 !p-0 inline-flex items-center justify-center cursor-pointer" aria-label="{{ __('pagination.previous') }}">
                            <x-mine.icon name="chevron-left" variant="micro" />
                        </button>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="pagination-item size-10 !p-0 inline-flex items-center justify-center opacity-50 cursor-default text-sm font-medium">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                    @if ($page == $paginator->currentPage())
                                        <span aria-current="page">
                                            <span class="pagination-item bg-(--mine-btn-ghost-bg-hover) size-10 !p-0 inline-flex items-center justify-center cursor-default text-sm font-medium">{{ $page }}</span>
                                        </span>
                                    @else
                                        <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="pagination-item size-10 !p-0 inline-flex items-center justify-center cursor-pointer text-sm font-medium" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                            {{ $page }}
                                        </button>
                                    @endif
                                </span>
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" class="pagination-item size-10 !p-0 inline-flex items-center justify-center cursor-pointer" aria-label="{{ __('pagination.next') }}">
                            <x-mine.icon name="chevron-right" variant="micro" />
                        </button>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="pagination-item size-10 !p-0 inline-flex items-center justify-center opacity-50 cursor-default" aria-hidden="true">
                                <x-mine.icon name="chevron-right" variant="micro" />
                            </span>
                        </span>
                    @endif
                </div>
            </div>
        </nav>
    @endif
</div>
