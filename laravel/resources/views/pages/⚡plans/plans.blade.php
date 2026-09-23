<div class="flex flex-1 flex-col px-4 pt-4 pb-6 sm:px-8 md:px-16">
    <x-mine.animate class="mb-6">
        <h1 class="mine-text-primary text-base font-bold">{{ __('My plans') }}</h1>
    </x-mine.animate>
    @island(name: 'plans-content', always: true)
        <x-mine.animate delay="50" class="mb-6 flex flex-col gap-2 sm:gap-4 md:flex-row">
            <div class="flex w-full items-center justify-between gap-2 sm:gap-4">
                <div class="w-full">
                    <x-mine.input
                        wire:model.live.debounce.200ms="search"
                        placeholder="{{ __('Search plans...') }}"
                        leftIcon="Magnifier"
                    />
                </div>
                <x-mine.modal.trigger id="add-plan-form">
                    <x-mine.button
                        type="button"
                        @class([
                            'sm:pr-4!' => app()->isLocale('en'),
                            'sm:pl-4!' => app()->isLocale('fa'),
                            'mine-btn-primary px-3.5! sm:px-3!',
                        ])
                    >
                        <div class="flex items-center justify-center gap-2">
                            <x-mine.icon
                                name="Plus"
                                @class([
                                    'mb-1' => app()->isLocale('en'),
                                ])
                                weight="filled"
                                size="16"
                            />
                            <p class="hidden text-sm font-medium sm:inline">
                                <span class="hidden sm:inline md:hidden">{{ __('New') }}</span
                                ><span class="hidden md:inline">{{ __('New plan') }}</span>
                            </p>
                        </div>
                    </x-mine.button>
                </x-mine.modal.trigger>
                <x-mine.dropdown group="plan-filter">
                    <x-mine.dropdown.trigger as="div">
                        <div @class([
                            'sm:pl-3!' => app()->isLocale('en'),
                            'sm:pr-3!' => app()->isLocale('fa'),
                            'w-full mine-btn-primary flex items-center h-11 px-5 sm:px-4 rounded-xl cursor-pointer select-none',
                        ])>
                            <x-mine.icon
                                name="ArrowUp"
                                @class([
                                    '-mr-1 -ml-2.5 sm:ml-0' => app()->isLocale('en'),
                                    '-ml-1 -mr-2.5 sm:mr-0' => app()->isLocale('fa'),
                                    'inline',
                                ])
                                weight="filled"
                                size="16"
                            />
                            <x-mine.icon
                                name="ArrowDown"
                                @class([
                                    '-ml-1 -mr-2.5 sm:mr-0' => app()->isLocale('en'),
                                    '-mr-1 -ml-2.5 sm:ml-0' => app()->isLocale('fa'),
                                    'inline',
                                ])
                                weight="filled"
                                size="16"
                            />
                            <p @class([
                                'sm:pl-1 pt-1' => app()->isLocale('en'),
                                'sm:pr-1' => app()->isLocale('fa'),
                                'text-sm font-medium hidden sm:inline',
                            ])>
                                <span class="hidden sm:inline md:hidden">{{ __('Sort') }}</span
                                ><span class="hidden md:inline">{{ __('Sort by') }}</span>
                            </p>
                        </div>
                    </x-mine.dropdown.trigger>
                    <x-mine.dropdown.content
                        class="mt-1!"
                        placement="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}"
                    >
                        @foreach ([
                            'state' => __('State'),
                            'deadline' => __('Deadline'),
                            'load' => __('Load'),
                            'latest' => __('Date created'),
                            'name' => __('Plan name'),
                        ] as $value => $label)
                            <x-mine.dropdown.item>
                                <div
                                    class="w-full rounded-lg py-2 px-4 flex items-center hover:cursor-pointer {{ $sort === $value ? 'bg-(--mine-dropdown-item-bg-hover)' : '' }}"
                                    @click="$wire.$island('plans-content').$set('sort', '{{ $value }}')"
                                >
                                    <p class="text-sm font-medium {{ app()->isLocale('en') ? 'pt-1' : '' }} {{ $sort === $value ? 'mine-text-link' : 'mine-text-secondary' }}">
                                        {{ $label }}
                                    </p>
                                </div>
                            </x-mine.dropdown.item>
                        @endforeach
                    </x-mine.dropdown.content>
                </x-mine.dropdown>
            </div>
            <div class="flex w-full justify-between gap-2 md:w-auto">
                <x-mine.dropdown group="plan-filter" class="w-full md:w-auto">
                    <x-mine.dropdown.trigger as="div" class="w-full md:w-auto">
                        <div @class([
                            'sm:pr-4!' => app()->isLocale('en'),
                            'sm:pl-4!' => app()->isLocale('fa'),
                            'w-full md:w-auto mine-btn-primary flex items-center justify-center gap-2 h-11 px-3.5 rounded-xl cursor-pointer select-none',
                        ])>
                            <x-mine.icon name="Filter" @class([
                            ]) weight="filled" size="16" />
                            <p @class([
                                'pt-1' => app()->isLocale('en'),
                                'text-sm font-medium',
                            ])>
                                {{ __('Filter') }}
                            </p>
                        </div>
                    </x-mine.dropdown.trigger>
                    <x-mine.dropdown.content
                        class="mt-1!"
                        placement="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}"
                    >
                        @foreach ([
                            'all' => __('All'),
                            'active' => __('Active'),
                            'completed' => __('Completed'),
                            'overdue' => __('Overdue'),
                        ] as $value => $label)
                            <x-mine.dropdown.item>
                                <div
                                    class="w-full rounded-lg py-2 px-4 flex items-center hover:cursor-pointer {{ $status_filter === $value ? 'bg-(--mine-dropdown-item-bg-hover)' : '' }}"
                                    @click="$wire.$island('plans-content').$set('status_filter', '{{ $value }}')"
                                >
                                    <p class="text-sm font-medium {{ app()->isLocale('en') ? 'pt-1' : '' }} {{ $status_filter === $value ? 'mine-text-link' : 'mine-text-secondary' }}">
                                        {{ $label }}
                                    </p>
                                </div>
                            </x-mine.dropdown.item>
                        @endforeach
                    </x-mine.dropdown.content>
                </x-mine.dropdown>
                <x-mine.dropdown group="plan-filter" class="w-full md:hidden">
                    <x-mine.dropdown.trigger as="div" class="w-full">
                        <div @class([
                            'sm:pr-4!' => app()->isLocale('en'),
                            'sm:pl-4!' => app()->isLocale('fa'),
                            'w-full mine-btn-primary flex items-center justify-center gap-2 h-11 px-3.5 rounded-xl cursor-pointer select-none',
                        ])>
                            <x-mine.icon name="Calendar" @class([
                            ]) weight="filled" size="16" />
                            <p @class([
                                'pt-1' => app()->isLocale('en'),
                                'text-sm font-medium',
                            ])>
                                {{ __('Calendar') }}
                            </p>
                        </div>
                    </x-mine.dropdown.trigger>
                    <x-mine.dropdown.content
                        class="mt-1! flex justify-center"
                        placement="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}"
                    >
                        <div class="flex w-80 items-center justify-center">
                            <x-mine.calendar wire:model.live="range_filter" island="plans-content" :card="false" />
                        </div>
                    </x-mine.dropdown.content>
                </x-mine.dropdown>
            </div>
        </x-mine.animate>
        <div class="md:flex md:gap-6">
            <div class="relative flex-1">
                <div wire:loading.delay.short class="absolute inset-0 z-10">
                    <div class="flex h-full w-full items-center justify-center">
                        <svg
                            class="mine-text-secondary size-8 animate-spin"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            role="status"
                            aria-label="Loading"
                        >
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                            />
                        </svg>
                    </div>
                </div>
                <div wire:loading.delay.short.class="opacity-40" class="transition-opacity">
                    <div class="grid grid-cols-1 gap-4">
                        @forelse ($this->plans as $plan)
                            @php
                                $pct = $plan->tasks_count > 0 ? (int) round($plan->tasks_done_count / $plan->tasks_count * 100) : 0;

                                $config = match (true) {
                                    $plan->done => [
                                        'icon' => 'Trophy',
                                        'header_class' => 'mine-badge-secondary',
                                        'title_class' => 'line-through',
                                        'status_label' => __('Completed'),
                                        'separator' => 'secondary',
                                        'card' => 'mine-card mine-card-secondary',
                                        'percent_color' => 'mine-text-secondary-accent',
                                        'span_color' => 'mine-text-secondary-accent',
                                        'progress_variant' => 'gray',
                                        'progress_total' => $plan->tasks_count ?: 1,
                                        'progress_value' => $plan->tasks_count ? $plan->tasks_done_count : 1,
                                        'pct' => $plan->tasks_count > 0 ? $pct : 100,
                                        'check_badge' => 'mine-badge-secondary',
                                        'xmark_badge' => 'mine-badge-secondary',
                                        'days' => null,
                                        'action' => 'reopen',
                                        'border-l' => 'border-l-6 border-l-(--mine-btn-secondary-bg) hover:border-l-(--mine-btn-secondary-bg-hover)',
                                        'border-r' => 'border-r-6 border-r-(--mine-btn-secondary-bg) hover:border-r-(--mine-btn-secondary-bg-hover)',
                                    ],
                                    $plan->finish_date->isPast() => [
                                        'icon' => 'Siren2',
                                        'header_class' => 'mine-badge-danger',
                                        'title_class' => '',
                                        'status_label' => __('Overdue'),
                                        'separator' => 'danger',
                                        'card' => 'mine-card mine-card-danger',
                                        'percent_color' => 'mine-text-error',
                                        'span_color' => 'mine-text-error',
                                        'progress_variant' => 'danger',
                                        'progress_total' => '100',
                                        'progress_value' => $pct,
                                        'pct' => $pct,
                                        'check_badge' => 'mine-badge-secondary',
                                        'xmark_badge' => 'mine-badge-danger',
                                        'days' => [
                                            'value' => (int) -now()->diffInDays($plan->finish_date),
                                            'label' => __('days overdue'),
                                            'badge' => 'mine-badge-danger',
                                        ],
                                        'action' => 'complete',
                                        'add_class' => 'mine-btn-outline-danger',
                                        'complete_class' => 'mine-btn-danger',
                                        'border-l' => 'border-l-6 border-l-(--mine-btn-danger-bg) hover:border-l-(--mine-btn-danger-bg-hover)',
                                        'border-r' => 'border-r-6 border-r-(--mine-btn-danger-bg) hover:border-r-(--mine-btn-danger-bg-hover)',
                                    ],
                                    default => [
                                        'icon' => 'Bullseye',
                                        'header_class' => 'mine-badge-primary',
                                        'title_class' => '',
                                        'status_label' => __('Active'),
                                        'separator' => 'primary',
                                        'card' => 'mine-card',
                                        'percent_color' => 'mine-text-link',
                                        'span_color' => 'mine-text-link',
                                        'progress_variant' => 'primary',
                                        'progress_total' => '100',
                                        'progress_value' => $pct,
                                        'pct' => $pct,
                                        'check_badge' => 'mine-badge-primary',
                                        'xmark_badge' => 'mine-badge-secondary',
                                        'days' => [
                                            'value' => max(0, (int) now()->diffInDays($plan->finish_date)),
                                            'label' => __('days left'),
                                            'badge' => 'mine-badge-primary',
                                        ],
                                        'action' => 'complete',
                                        'add_class' => 'mine-btn-outline-primary',
                                        'complete_class' => 'mine-btn-primary',
                                        'border-l' => 'border-l-6 border-l-(--mine-btn-primary-bg) hover:border-l-(--mine-btn-primary-bg-hover)',
                                        'border-r' => 'border-r-6 border-r-(--mine-btn-primary-bg) hover:border-r-(--mine-btn-primary-bg-hover)',
                                    ],
                                };
                            @endphp

                            <x-mine.animate
                                wire:key="plan-{{ $plan->id }}"
                                stagger="60"
                                data-anim-index="{{ $loop->index }}"
                                :class="\Illuminate\Support\Arr::toCssClasses([
                                $config['border-l'] => app()->isLocale('en'),
                                $config['border-r'] => app()->isLocale('fa'),
                            ]) . ' self-start ' . $config['card'] . ' flex flex-col px-4 sm:px-6 md:px-8 py-4 sm:pb-5 md:pb-6 sm:pt-6 md:pt-8'"
                            >
                                <div class="mb-8 flex min-w-0 justify-between gap-4">
                                    <div class="flex min-w-0 flex-1">
                                        <div @class([
                                            'mr-4' => app()->isLocale('en'),
                                            'ml-4' => app()->isLocale('fa'),
                                            'shrink-0 '.$config['header_class'].' rounded-xl size-16 flex justify-center items-center',
                                        ])>
                                            <x-mine.icon :name="$config['icon']" weight="filled" size="32" />
                                        </div>
                                        <div class="flex min-w-0 flex-col justify-between gap-2">
                                            <div class="flex min-w-0 items-center gap-4">
                                                <h1 @class([
                                                    'pt-1' => app()->isLocale('en'),
                                                    'text-base font-semibold mine-text-primary truncate min-w-0 '.$config['title_class'],
                                                ])>
                                                    {{ $plan->name }}
                                                </h1>
                                                <div class="{{ $config['header_class'] }} rounded-xl h-7 flex justify-center items-center px-3 shrink-0">
                                                    <p @class([
                                                        'pt-1' => app()->isLocale('en'),
                                                        'text-[13px] font-semibold',
                                                    ])>
                                                        {{ $config['status_label'] }}
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="text-sm font-medium mine-text-secondary min-w-0 line-clamp-2 {{ $config['title_class'] }}">
                                                {{ $plan->description ?: __('No description') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex h-full shrink-0 items-start justify-end">
                                        <x-mine.dropdown group="plan-actions">
                                            <x-mine.dropdown.trigger>
                                                <div class="mine-btn-icon rounded-xl p-2">
                                                    <x-mine.icon name="MoreH" size="18" weight="filled" />
                                                </div>
                                            </x-mine.dropdown.trigger>
                                            <x-mine.dropdown.content placement="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}">
                                                <x-mine.dropdown.item href="{{ route('tasks', ['plan_filter' => [$plan->id]]) }}">
                                                    <div class="mine-text-link flex w-full items-center px-4 py-2">
                                                        <p class="text-sm font-medium">{{ __('View tasks') }}</p>
                                                    </div>
                                                </x-mine.dropdown.item>
                                                <x-mine.dropdown.divider class="-mx-1" />
                                                <x-mine.dropdown.item>
                                                    <div
                                                        class="mine-text-link flex w-full items-center px-4 py-2 hover:cursor-pointer"
                                                        @click.stop="$wire.set('editing_id', {{ $plan->id }}, false);
                                                            $wire.set('edit_name', @js($plan->name), false);
                                                            $wire.set('edit_description', @js($plan->description), false);
                                                            $wire.set('edit_range', @js(['start' => $plan->start_date?->format('Y-m-d'), 'end' => $plan->finish_date?->format('Y-m-d')]), false);
                                                            $dispatch('open-modal', { id: 'edit-plan-form' })"
                                                    >
                                                        <p @class([
                                                            'pt-0.5' => app()->isLocale('en'),
                                                            'text-sm font-medium',
                                                        ])>
                                                            {{ __('Edit') }}
                                                        </p>
                                                    </div>
                                                </x-mine.dropdown.item>
                                                <x-mine.dropdown.item destructive>
                                                    <div
                                                        class="mine-text-error flex w-full items-center px-4 py-2 hover:cursor-pointer"
                                                        @click.stop="$wire.set('deleting_id', {{ $plan->id }}, false);
                                                            $dispatch('open-modal', { id: 'delete-plan-confirmation' })"
                                                    >
                                                        <p @class([
                                                            'pt-0.5' => app()->isLocale('en'),
                                                            'text-sm font-medium',
                                                        ])>
                                                            {{ __('Delete') }}
                                                        </p>
                                                    </div>
                                                </x-mine.dropdown.item>
                                            </x-mine.dropdown.content>
                                        </x-mine.dropdown>
                                    </div>
                                </div>
                                <div class="mb-4 flex flex-wrap items-center justify-between gap-x-6 gap-y-4">
                                    @if ($config['days'])
                                        <div class="flex items-center">
                                            <div @class([
                                                'pl-3 pr-4' => app()->isLocale('en'),
                                                'pr-3 pl-4' => app()->isLocale('fa'),
                                                $config['days']['badge'].' rounded-xl h-9 flex justify-center items-center gap-2',
                                            ])>
                                                <x-mine.icon name="Clock" size="18" weight="filled" />
                                                <p @class([
                                                    'pt-1' => app()->isLocale('en'),
                                                    'text-[13px] font-medium',
                                                ])>
                                                    {{ app()->isLocale('fa') ? App\Support\PersianNumber::convert($config['days']['value']) : $config['days']['value'] }} {{ $config['days']['label'] }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="flex min-w-0 items-center">
                                        <div @class([
                                            'pl-3 pr-4' => app()->isLocale('en'),
                                            'pr-3 pl-4' => app()->isLocale('fa'),
                                            $config['header_class'].' rounded-xl h-9 flex justify-center items-center shrink-0',
                                        ])>
                                            <x-mine.icon name="Calendar" size="18" weight="filled" />
                                            <p @class([
                                                'pt-1 ml-2' => app()->isLocale('en'),
                                                'mr-2' => app()->isLocale('fa'),
                                                'text-[13px] font-medium',
                                            ])>
                                                {{
                                                    app()->isLocale('fa')
                                                    ? \App\Support\Jalali::format($plan->start_date, 'd MMM ، y')
                                                    : $plan->start_date->format('d M , Y')
                                                }}
                                            </p>
                                            <div class="mx-4 flex items-center justify-center">
                                                <x-mine.icon
                                                    name="Arrow{{ app()->isLocale('en') ? 'Right' : 'Left' }}"
                                                    size="18"
                                                    weight="filled"
                                                />
                                            </div>
                                            <p @class([
                                                'pt-1' => app()->isLocale('en'),
                                                'text-[13px] font-medium',
                                            ])>
                                                {{
                                                    app()->isLocale('fa')
                                                    ? \App\Support\Jalali::format($plan->finish_date, 'd MMM ، y')
                                                    : $plan->finish_date->format('d M , Y')
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4 opacity-40">
                                    <x-mine.separator :variant="$config['separator']" />
                                </div>
                                <div class="mb-4 flex">
                                    <div class="flex flex-3 flex-col justify-between">
                                        <h2 class="mine-text-secondary mb-2 text-[13px] font-medium">
                                            {{ __('Progress') }}
                                        </h2>
                                        <h2 class="text-2xl font-bold {{ $config['percent_color'] }} mb-2">
                                            {{ app()->isLocale('fa') ? App\Support\PersianNumber::convert($config['pct']) : $config['pct'] }}%
                                        </h2>
                                        <x-mine.progress
                                            total="{{ $config['progress_total'] }}"
                                            progress="{{ $config['progress_value'] }}"
                                            variant="{{ $config['progress_variant'] ?? 'primary' }}"
                                            class="h-3"
                                        />
                                    </div>
                                    <div class="flex flex-1 items-center justify-center"></div>
                                </div>
                                <div class="mb-4 opacity-40">
                                    <x-mine.separator :variant="$config['separator']" />
                                </div>
                                <div class="flex items-center justify-between gap-4">
                                    @if ($config['action'] === 'reopen')
                                        <x-mine.button
                                            type="button"
                                            class="mine-btn-secondary"
                                            @click.stop="$wire.set('reopening_id', {{ $plan->id }}, false); $dispatch('open-modal', { id: 'reopen-plan-confirmation' })"
                                        >
                                            <div class="flex items-center justify-center gap-2 text-sm font-medium">
                                                <x-mine.icon
                                                    name="ArrowRotate"
                                                    size="16"
                                                    weight="filled"
                                                    @class([
                                                        'mb-1' => app()->isLocale('en'),
                                                    ])
                                                />
                                                <p>{{ __('Re active plan') }}</p>
                                            </div>
                                        </x-mine.button>
                                    @else
                                        <a
                                            href="{{ route('tasks', ['plan_filter' => [$plan->id], 'add_plan' => $plan->id]) }}"
                                            wire:navigate.hover
                                            class="w-full"
                                        >
                                            <x-mine.button type="button" class="{{ $config['add_class'] }}">
                                                <div class="font-md flex items-center justify-center gap-2 text-sm">
                                                    <x-mine.icon
                                                        name="Plus"
                                                        size="16"
                                                        weight="filled"
                                                        @class([
                                                            'mb-1' => app()->isLocale('en'),
                                                        ])
                                                    />
                                                    <p>{{ __('Add task') }}</p>
                                                </div>
                                            </x-mine.button>
                                        </a>
                                        <x-mine.button
                                            type="button"
                                            class="{{ $config['complete_class'] }}"
                                            @click.stop="$wire.set('completing_id', {{ $plan->id }}, false); $dispatch('open-modal', { id: 'complete-plan-confirmation' })"
                                        >
                                            <div class="flex items-center justify-center gap-2 text-sm font-medium">
                                                <x-mine.icon
                                                    name="Check"
                                                    size="16"
                                                    weight="filled"
                                                    @class([
                                                        'mb-1' => app()->isLocale('en'),
                                                    ])
                                                />
                                                <p class="hidden sm:block">{{ __('Mark as completed') }}</p>
                                                <p class="sm:hidden">{{ __('Complete') }}</p>
                                            </div>
                                        </x-mine.button>
                                    @endif
                                </div>
                            </x-mine.animate>
                        @empty
                            @if ($this->hasActiveFilters)
                                <x-mine.animate class="col-span-full py-12 text-center">
                                    <x-mine.icon
                                        name="Magnifier"
                                        weight="filled"
                                        size="40"
                                        class="mine-text-secondary mx-auto mb-3"
                                    />
                                    <p class="mine-text-secondary text-sm font-medium">{{ __('No plans found') }}</p>
                                </x-mine.animate>
                            @else
                                <x-mine.animate class="col-span-full py-12 text-center">
                                    <x-mine.icon
                                        name="Bullseye"
                                        size="40"
                                        weight="filled"
                                        class="mine-text-secondary mx-auto mb-3"
                                    />
                                    <p class="mine-text-secondary text-sm font-medium">{{ __('No plans yet') }}</p>
                                </x-mine.animate>
                            @endif
                        @endforelse
                    </div>
                </div>
            </div>
            <x-mine.animate delay="100" class="hidden w-80 justify-center md:flex">
                <x-mine.calendar wire:model.live="range_filter" island="plans-content" />
            </x-mine.animate>
        </div>
        <x-mine.animate class="mt-6 w-full"> {{ $this->plans->links(data: ['scrollTo' => false]) }} </x-mine.animate>
    @endisland

    <x-mine.modal id="add-plan-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 py-6 sm:px-8">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="mine-text-primary text-base font-semibold">{{ __('Add new plan') }}</h2>
                <button
                    type="button"
                    @click="
                        close();
                        $wire.cancelAdd();
                    "
                    class="mine-btn-icon rounded-xl p-2"
                >
                    <x-mine.icon name="Xmark" weight="filled" size="16" />
                </button>
            </div>

            @if ($add_error === 'already_exists')
                <div class="mb-4">
                    <x-mine.alert
                        variant="danger"
                        title="{{ __('Duplicate plan!') }}"
                    >
                        {{ __('You have a plan with this name.') }}</x-mine.alert>
                </div>
            @endif

            @if ($add_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert
                        variant="warning"
                        title="{{ __('Too many add-plan attempts!') }}"
                    >
                        {{ __('Try again in a minute.') }}</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="addPlan">
                <div>
                    <x-mine.input
                        label="{{ __('Plan name') }}"
                        wire:model="add_name"
                        placeholder="{{ __('Your plan name') }}"
                        leftIcon="Bullseye"
                    />
                </div>
                <div>
                    <x-mine.datepicker
                        leftIcon="Calendar"
                        mode="range"
                        position="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}"
                        wire:model="add_range"
                        label="{{ __('Plan range') }}"
                    />
                </div>
                <div>
                    <x-mine.textarea
                        label="{{ __('Plan description') }}"
                        wire:model="add_description"
                        placeholder="{{ __('Your plan description') }}"
                        leftIcon="PenWriting"
                    />
                </div>
                <div class="mt-4 flex justify-end gap-4">
                    <x-mine.button
                        type="button"
                        @click="
                            close();
                            $wire.cancelAdd();
                        "
                        class="mine-btn-ghost"
                    >
                        <p class="text-sm">{{ __('Cancel') }}</p>
                    </x-mine.button>
                    <x-mine.button wire:target="addPlan" class="mine-btn-primary">
                        <p class="text-sm">{{ __('Create') }}</p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="edit-plan-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 py-6 sm:px-8">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="mine-text-primary text-base font-semibold">{{ __('Edit plan') }}</h2>
                <button
                    type="button"
                    @click="
                        close();
                        $wire.cancelEdit();
                    "
                    class="mine-btn-icon rounded-xl p-2"
                >
                    <x-mine.icon name="Xmark" weight="filled" size="16" />
                </button>
            </div>

            @if ($edit_error === 'already_exists')
                <div class="mb-4">
                    <x-mine.alert
                        variant="danger"
                        title="{{ __('Duplicate plan!') }}"
                    >
                        {{ __('You have a plan with this name.') }}</x-mine.alert>
                </div>
            @endif

            @if ($edit_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert
                        variant="warning"
                        title="{{ __('Too many edit-plan attempts!') }}"
                    >
                        {{ __('Try again in a minute.') }}</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="editPlan">
                <div>
                    <x-mine.input
                        leftIcon="Bullseye"
                        label="{{ __('Plan name') }}"
                        wire:model="edit_name"
                        placeholder="{{ __('Your plan name') }}"
                    />
                </div>
                <div>
                    <x-mine.datepicker
                        leftIcon="Calendar"
                        mode="range"
                        position="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}"
                        wire:model="edit_range"
                        label="{{ __('Plan range') }}"
                    />
                </div>
                <div>
                    <x-mine.textarea
                        label="{{ __('Plan description') }}"
                        wire:model="edit_description"
                        placeholder="{{ __('Your plan description') }}"
                        leftIcon="PenWriting"
                    />
                </div>
                <div class="mt-4 flex justify-end gap-4">
                    <x-mine.button
                        type="button"
                        @click="
                            close();
                            $wire.cancelEdit();
                        "
                        class="mine-btn-ghost"
                    >
                        <p class="text-sm">{{ __('Cancel') }}</p>
                    </x-mine.button>
                    <x-mine.button wire:target="editPlan" class="mine-btn-primary">
                        <p class="text-sm">{{ __('Save') }}</p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="delete-plan-confirmation" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 py-6 sm:px-8">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="mine-text-primary text-base font-semibold">{{ __('Delete plan') }}</h2>
                <button
                    type="button"
                    @click="
                        close();
                        $wire.cancelDelete();
                    "
                    class="mine-btn-icon rounded-xl p-2"
                >
                    <x-mine.icon name="Xmark" weight="filled" size="16" />
                </button>
            </div>

            @if ($delete_error === 'has_tasks')
                <div class="mb-4">
                    <x-mine.alert
                        variant="danger"
                        title="{{ __('Cannot delete plan!') }}"
                    >
                        {{ __('This plan has tasks, reassign or delete them first.') }}</x-mine.alert>
                </div>
            @else
                <div class="mb-4">
                    <x-mine.alert
                        variant="warning"
                        title="{{ __('Are you sure?') }}"
                    >
                        {{ __('This action cannot be undone.') }}</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="deletePlan">
                <div class="mt-4 flex justify-end gap-4">
                    <x-mine.button
                        type="button"
                        @click="
                            close();
                            $wire.cancelDelete();
                        "
                        class="mine-btn-ghost"
                    >
                        <p class="text-sm">{{ __('Cancel') }}</p>
                    </x-mine.button>
                    <x-mine.button wire:target="deletePlan" class="mine-btn-danger">
                        <p class="text-sm">{{ __('Delete') }}</p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="complete-plan-confirmation" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 py-6 sm:px-8">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="mine-text-primary text-base font-semibold">{{ __('Complete plan') }}</h2>
                <button
                    type="button"
                    @click="
                        close();
                        $wire.cancelComplete();
                    "
                    class="mine-btn-icon rounded-xl p-2"
                >
                    <x-mine.icon name="Xmark" weight="filled" size="16" />
                </button>
            </div>

            @if ($complete_error === 'has_undone_tasks')
                <div class="mb-4">
                    <x-mine.alert
                        variant="danger"
                        title="{{ __('Cannot complete plan!') }}"
                    >
                        {{ __('This plan still has unfinished tasks, complete or remove them first.') }}</x-mine.alert>
                </div>
            @else
                <div class="mb-4">
                    <x-mine.alert
                        variant="warning"
                        title="{{ __('Are you sure?') }}"
                    >
                        {{ __('This will mark the plan as completed.') }}</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="completePlan">
                <div class="mt-4 flex justify-end gap-4">
                    <x-mine.button
                        type="button"
                        @click="
                            close();
                            $wire.cancelComplete();
                        "
                        class="mine-btn-ghost"
                    >
                        <p class="text-sm">{{ __('Cancel') }}</p>
                    </x-mine.button>
                    <x-mine.button wire:target="completePlan" class="mine-btn-primary">
                        <p class="text-sm">{{ __('Complete') }}</p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="reopen-plan-confirmation" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 py-6 sm:px-8">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="mine-text-primary text-base font-semibold">{{ __('Re active plan') }}</h2>
                <button
                    type="button"
                    @click="
                        close();
                        $wire.cancelReopen();
                    "
                    class="mine-btn-icon rounded-xl p-2"
                >
                    <x-mine.icon name="Xmark" weight="filled" size="16" />
                </button>
            </div>

            <div class="mb-4">
                <x-mine.alert
                    variant="warning"
                    title="{{ __('Are you sure?') }}"
                >
                    {{ __('This plan will be moved back to active.') }}</x-mine.alert>
            </div>

            <form class="flex flex-col gap-4" wire:submit="reopenPlan">
                <div class="mt-4 flex justify-end gap-4">
                    <x-mine.button
                        type="button"
                        @click="
                            close();
                            $wire.cancelReopen();
                        "
                        class="mine-btn-ghost"
                    >
                        <p class="text-sm">{{ __('Cancel') }}</p>
                    </x-mine.button>
                    <x-mine.button wire:target="reopenPlan" class="mine-btn-primary">
                        <p class="text-sm">{{ __('Re active') }}</p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>
</div>
