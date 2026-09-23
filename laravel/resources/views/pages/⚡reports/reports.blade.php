@php
    $fa = fn (int|string|float|null $value): string => app()->isLocale('fa')
        ? \App\Support\PersianNumber::convert($value)
        : (string) $value;
@endphp
<div class="flex flex-1 flex-col px-4 pt-4 pb-6 sm:px-8 md:px-16">
    <x-mine.animate class="mb-6">
        <h1 class="mine-text-primary text-base font-bold">{{ __('My reports') }}</h1>
    </x-mine.animate>
    <div class="flex flex-col">
        <x-mine.animate delay="50" class="mb-6 flex flex-col items-center justify-between gap-4 md:flex-row">
            <div class="flex w-full flex-wrap items-center gap-2">
                @foreach ([
                    'this_week' => __('This week'),
                    'last_week' => __('Last week'),
                    'this_month' => __('This month'),
                    'last_month' => __('Last month'),
                    'custom' => __('Custom'),
                ] as $value => $label)
                    <x-mine.button
                        type="button"
                        wire:click="selectPreset('{{ $value }}')"
                        @class([
                            'w-auto! flex-1 text-sm! font-medium h-10!',
                            'mine-btn-primary' => $preset === $value,
                            'mine-btn-ghost' => $preset !== $value,
                        ])
                    >
                        {{ $label }}
                    </x-mine.button>
                @endforeach
                <div class="ms-aut flex flex-1 items-center justify-end gap-2">
                    @if ($preset === 'custom')
                        <x-mine.dropdown group="reports-range" class="w-full flex-1">
                            <x-mine.dropdown.trigger as="div" class="w-full">
                                <div @class([
                                    'sm:pr-4!' => app()->isLocale('en'),
                                    'sm:pl-4!' => app()->isLocale('fa'),
                                    'w-full mine-btn-primary flex items-center justify-center gap-2 h-10 px-5 rounded-xl cursor-pointer select-none',
                                ])>
                                    <x-mine.icon name="Calendar" size="16" weight="filled" />
                                    <p @class([
                                        'pt-1' => app()->isLocale('en'),
                                        'text-sm font-medium',
                                    ])>
                                        {{ __('Date range') }}
                                    </p>
                                </div>
                            </x-mine.dropdown.trigger>
                            <x-mine.dropdown.content
                                class="mt-1! flex justify-center"
                                placement="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}"
                            >
                                <div class="flex w-80 items-center justify-center">
                                    <x-mine.calendar wire:model.live="range_filter" :card="false" />
                                </div>
                            </x-mine.dropdown.content>
                        </x-mine.dropdown>
                    @endif
                </div>
            </div>
        </x-mine.animate>
        <div class="mb-6 flex flex-col">
            <x-mine.animate>
                <h2 class="mine-text-primary mb-4 text-sm font-semibold">{{ __('Tasks Summary') }}</h2></x-mine.animate>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4">
                <x-mine.animate
                    stagger="50"
                    data-anim-index="0"
                    class="mine-card flex items-center justify-between p-4"
                >
                    <div class="flex flex-col justify-between gap-4">
                        <p class="mine-text-secondary text-[13px] font-medium">{{ __('Total Tasks') }}</p>
                        <p class="mine-text-primary text-sm font-semibold">{{ $fa($this->stats['total_tasks']) }}</p>
                    </div>
                    <div class="mine-badge-primary flex size-12 items-center justify-center rounded-full">
                        <x-mine.icon name="Clipboard" weight="filled" size="24" />
                    </div>
                </x-mine.animate>
                <x-mine.animate
                    stagger="50"
                    data-anim-index="1"
                    class="mine-card flex items-center justify-between p-4"
                >
                    <div class="flex flex-col justify-between gap-4">
                        <p class="mine-text-secondary text-[13px] font-medium">{{ __('Completed Tasks') }}</p>
                        <p class="mine-text-primary text-sm font-semibold">
                            {{ $fa($this->stats['completed_tasks']) }}
                        </p>
                    </div>
                    <div class="mine-badge-primary flex size-12 items-center justify-center rounded-full">
                        <x-mine.icon name="ClipboardCheck" weight="filled" size="24" />
                    </div>
                </x-mine.animate>
                <x-mine.animate
                    stagger="50"
                    data-anim-index="2"
                    class="mine-card flex items-center justify-between p-4"
                >
                    <div class="flex flex-col justify-between gap-4">
                        <p class="mine-text-secondary text-[13px] font-medium">{{ __('Completion Rate') }}</p>
                        <p class="mine-text-primary text-sm font-semibold">
                            {{ $fa($this->stats['completion_rate']) }}%
                        </p>
                    </div>
                    <div class="mine-badge-primary flex size-12 items-center justify-center rounded-full">
                        <x-mine.icon name="Pie2" weight="filled" size="24" />
                    </div>
                </x-mine.animate>
                <x-mine.animate
                    stagger="50"
                    data-anim-index="3"
                    class="mine-card flex items-center justify-between p-4"
                >
                    <div class="flex flex-col justify-between gap-4">
                        <p class="mine-text-secondary text-[13px] font-medium">{{ __('Estimated Time') }}</p>
                        <p class="mine-text-primary text-sm font-semibold">{{ $fa($this->stats['estimated_time']) }}</p>
                    </div>
                    <div class="mine-badge-primary flex size-12 items-center justify-center rounded-full">
                        <x-mine.icon name="Clock" weight="filled" size="24" />
                    </div>
                </x-mine.animate>
            </div>
        </div>
        <div class="mb-10 flex flex-col">
            <x-mine.animate delay="60">
                <h2 class="mine-text-primary mb-4 text-sm font-semibold">{{ __('Plans Summary') }}</h2></x-mine.animate>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4">
                <x-mine.animate
                    delay="100"
                    stagger="50"
                    data-anim-index="0"
                    class="mine-card flex items-center justify-between p-4"
                >
                    <div class="flex flex-col justify-between gap-4">
                        <p class="mine-text-secondary text-[13px] font-medium">{{ __('Total Plans') }}</p>
                        <p class="mine-text-primary text-sm font-semibold">{{ $fa($this->stats['total_plans']) }}</p>
                    </div>
                    <div class="mine-badge-primary flex size-12 items-center justify-center rounded-full">
                        <x-mine.icon name="Bullseye" weight="filled" size="24" />
                    </div>
                </x-mine.animate>
                <x-mine.animate
                    delay="100"
                    stagger="50"
                    data-anim-index="1"
                    class="mine-card flex items-center justify-between p-4"
                >
                    <div class="flex flex-col justify-between gap-4">
                        <p class="mine-text-secondary text-[13px] font-medium">{{ __('Completed Plans') }}</p>
                        <p class="mine-text-primary text-sm font-semibold">
                            {{ $fa($this->stats['completed_plans']) }}
                        </p>
                    </div>
                    <div class="mine-badge-primary flex size-12 items-center justify-center rounded-full">
                        <x-mine.icon name="Trophy" weight="filled" size="24" />
                    </div>
                </x-mine.animate>
            </div>
        </div>
        <div class="flex flex-col">
            <x-mine.animate delay="120">
                <div class="flex items-center justify-between">
                    <h2 class="mine-text-primary mb-4 text-sm font-semibold">{{ __('Workload Chart') }}</h2>
                    <div class="mb-4 flex items-center justify-end gap-4">
                        <div class="flex items-center justify-center gap-2">
                            <span class="mt-0.5 h-2 w-2 rounded-full bg-(--mine-btn-primary-bg)"></span>
                            <p class="mine-text-secondary text-xs font-medium">{{ __('Completed') }}</p>
                        </div>
                        <div class="flex items-center justify-center gap-2">
                            <span class="mt-0.5 h-2 w-2 rounded-full bg-(--mine-btn-danger-bg)"></span>
                            <p class="mine-text-secondary text-xs font-medium">{{ __('Remaining') }}</p>
                        </div>
                    </div>
                </div>
            </x-mine.animate>
            <x-mine.animate delay="160" class="mine-card p-4">
                <x-mine.bar-chart
                    :items="$this->chart"
                    label-key="label"
                    :series="[
                        ['key' => 'completed', 'class' => 'bg-(--mine-btn-primary-bg)', 'label' => __('Completed')],
                        ['key' => 'remaining', 'class' => 'bg-(--mine-btn-danger-bg)', 'label' => __('Remaining')],
                    ]"
                    :format="$this->formatMinutes(...)"
                />
            </x-mine.animate>
        </div>
    </div>
</div>
