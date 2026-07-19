@props([
    'value' => null,
    'label' => null,
    'searchLabel' => null,
    'disabled' => false,
])

@aware(['height' => 'h-11'])

@php
    $value = filled($value) ? $value : trim($slot->__toString());
    $label = filled($label) ? $label : trim($slot->__toString());
    $searchLabel = filled($searchLabel) ? $searchLabel : $label;
@endphp

<li
    role="option"
    tabindex="-1"
    data-value="{{ $value }}"
    data-label="{{ $label }}"
    data-search="{{ $searchLabel }}"
    x-on:click="select($el.dataset.value, $el.dataset.label)"
    x-on:keydown="handleOptionKeydown($event)"
    x-show="!search || $el.dataset.search.toLowerCase().includes(search.toLowerCase())"
    :data-selected="state === $el.dataset.value ? 'true' : 'false'"
    :class="{
        'bg-[var(--mine-select-selected-bg)]': state === $el.dataset.value,
        'text-[var(--mine-select-selected-text)] font-medium': state === $el.dataset.value,
        'hover:bg-[var(--mine-select-bg-hover)] mine-text-primary': state !== $el.dataset.value,
    }"
    @class([
        'flex items-center rounded-lg px-3 text-sm cursor-pointer transition-colors duration-150 outline-none focus-visible:ring-4 focus-visible:ring-[var(--mine-select-ring-focus)]',
        'py-2.5' => $height === 'h-12',
        $height => $height !== 'h-12',
        'opacity-50 cursor-not-allowed' => $disabled,
    ])
    @if($disabled) aria-disabled="true" @endif
>
    <div
        :class="state === $el.closest('[role=option]').dataset.value ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
        class="size-4 mr-3 shrink-0 transition-all duration-150"
    >
        <x-mine.icon name="check" variant="micro" class="size-4" />
    </div>

    <span class="">
        {{ $slot }}
    </span>
</li>
