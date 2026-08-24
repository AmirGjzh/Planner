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
        <nav role="navigation" @class([
            "md:pl-5" => app()->isLocale('en'),
            "md:pr-5" => app()->isLocale('fa'),
            "w-full mine-card h-16 flex items-center px-3"
        ])>
            {{-- Mobile view --}}
            <div class="w-full flex justify-between md:hidden">
                @if ($paginator->onFirstPage())
                    <span @class([
                        "pr-4 pl-3" => app()->isLocale('en'),
                        "pl-4 pr-3" => app()->isLocale('fa'),
                        "text-[13px] font-medium mine-pagination-item py-2 text-(--mine-pagination-text-disabled) opacity-70 cursor-default flex gap-1 items-center"
                    ])>
                        <x-mine.icon name="chevron-double-{{ app()->isLocale('en') ? 'left' : 'right' }}" variant="micro" class="size-4" />
                        <p @class(["pt-1" => app()->isLocale('en'), "pt-0.5" => app()->isLocale('fa')])>
                            {{ __('Previous') }}
                        </p>
                    </span>
                @else
                    <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" @class([
                        "pr-4 pl-3" => app()->isLocale('en'),
                        "pl-4 pr-3" => app()->isLocale('fa'),
                        "text-[13px] font-medium mine-pagination-item py-2 flex gap-1 items-center cursor-pointer"
                    ])>
                        <x-mine.icon name="chevron-double-{{ app()->isLocale('en') ? 'left' : 'right' }}" variant="micro" class="size-4" />
                        <p @class(["pt-1" => app()->isLocale('en'), "pt-0.5" => app()->isLocale('fa')])>
                            {{ __('Previous') }}
                        </p>
                    </button>
                @endif

                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" @class([
                        "pl-4 pr-3" => app()->isLocale('en'),
                        "pr-4 pl-3" => app()->isLocale('fa'),
                        "text-[13px] font-medium mine-pagination-item py-2 flex gap-1 items-center cursor-pointer"
                    ])>
                        <p @class(["pt-1" => app()->isLocale('en'), "pt-0.5" => app()->isLocale('fa')])>
                            {{ __('Next') }}
                        </p>
                        <x-mine.icon name="chevron-double-{{ app()->isLocale('fa') ? 'left' : 'right' }}" variant="micro" class="size-4" />
                    </button>
                @else
                    <span @class([
                        "pl-4 pr-3" => app()->isLocale('en'),
                        "pr-4 pl-3" => app()->isLocale('fa'),
                        "text-[13px] font-medium mine-pagination-item py-2 flex gap-1 items-center text-(--mine-pagination-text-disabled) opacity-70 cursor-default"
                    ])>
                        <p @class(["pt-1" => app()->isLocale('en'), "pt-0.5" => app()->isLocale('fa')])>
                            {{ __('Next') }}
                        </p>
                        <x-mine.icon name="chevron-double-{{ app()->isLocale('fa') ? 'left' : 'right' }}" variant="micro" class="size-4" />
                    </span>
                @endif
            </div>

            {{-- Desktop view --}}
            <div class="hidden md:flex md:items-center md:justify-between w-full">
                <div class="shrink-0">
                    <p @class([
                        "pt-1" => app()->isLocale('en'),
                        "pt-0.5" => app()->isLocale('fa'),
                        "text-[13px] mine-text-secondary font-medium"
                    ])>
                        <span>{!! __('Showing') !!}</span>
                        <span class="mine-text-link">{{ app()->isLocale('en') ? $paginator->firstItem() : App\Support\PersianNumber::show($paginator->firstItem()) }}</span>
                        <span>{!! __('to') !!}</span>
                        <span class="mine-text-link">{{ app()->isLocale('en') ? $paginator->lastItem() : App\Support\PersianNumber::show($paginator->lastItem()) }}</span>
                        <span>{!! __('of') !!}</span>
                        <span class="mine-text-link">{{ app()->isLocale('en') ? $paginator->total() : App\Support\PersianNumber::show($paginator->total()) }}</span>
                    </p>
                </div>

                <div class="flex items-center gap-1.5">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true">
                            <span class="mine-pagination-item size-10 !p-0 inline-flex items-center justify-center text-(--mine-pagination-text-disabled) opacity-70 cursor-default" aria-hidden="true">
                                <x-mine.icon name="chevron-{{ app()->isLocale('en') ? 'left' : 'right' }}" class="size-4" variant="micro" />
                            </span>
                        </span>
                    @else
                        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" class="mine-pagination-item size-10 !p-0 inline-flex items-center justify-center cursor-pointer">
                            <x-mine.icon name="chevron-{{ app()->isLocale('en') ? 'left' : 'right' }}" class="size-4" variant="micro" />
                        </button>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="mine-pagination-item size-10 !p-0 inline-flex items-center justify-center text-(--mine-pagination-text-disabled) opacity-70 cursor-default text-[13px] font-medium">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                    @if ($page == $paginator->currentPage())
                                        <span aria-current="page">
                                            <span @class([
                                                "mine-pagination-item border-(--mine-btn-primary-bg) mine-btn-primary!  size-10 !p-0 inline-flex items-center justify-center cursor-default text-[13px] font-medium"
                                            ])>
                                                <p @class([
                                                    "pt-1" => app()->isLocale('en'),
                                                    "pt-0.5" => app()->isLocale('fa'),
                                                ])>
                                                    {{ app()->isLocale('en') ? $page : App\Support\PersianNumber::show($page) }}
                                                </p>
                                            </span>
                                        </span>
                                    @else
                                        <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="mine-pagination-item size-10 !p-0 inline-flex items-center justify-center cursor-pointer text-[13px] font-medium">
                                            <p @class([
                                                "pt-1" => app()->isLocale('en'),
                                                "pt-0.5" => app()->isLocale('fa'),
                                            ])>
                                                {{ app()->isLocale('en') ? $page : App\Support\PersianNumber::show($page) }}
                                            </p>
                                        </button>
                                    @endif
                                </span>
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" class="mine-pagination-item size-10 !p-0 inline-flex items-center justify-center cursor-pointer">
                            <x-mine.icon name="chevron-{{ app()->isLocale('fa') ? 'left' : 'right' }}" class="size-4" variant="micro" />
                        </button>
                    @else
                        <span aria-disabled="true">
                            <span class="mine-pagination-item size-10 !p-0 inline-flex items-center justify-center text-(--mine-pagination-text-disabled) opacity-70 cursor-default" aria-hidden="true">
                                <x-mine.icon name="chevron-{{ app()->isLocale('fa') ? 'left' : 'right' }}" class="size-4" variant="micro" />
                            </span>
                        </span>
                    @endif
                </div>
            </div>
        </nav>
    @endif
</div>
