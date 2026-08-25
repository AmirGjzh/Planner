<div class="flex-1 px-4 sm:px-8 md:px-16 pb-6 pt-4 flex flex-col">
    <div class="mb-6">
        <h1 class="font-bold text-base mine-text-primary">{{ __('My plans') }}</h1>
    </div>
    @island(name: 'plans-content', always: true)
    <div class="flex flex-col md:flex-row gap-2 sm:gap-4 mb-6">
        <div class="w-full flex items-center justify-between gap-2 sm:gap-4">
            <div class="w-full">
                <x-mine.input wire:model.live.debounce.200ms="search" placeholder="{{ __('Search plans...') }}"
                    leftIcon="magnifying-glass" />
            </div>
            <x-mine.modal.trigger id="add-plan-form">
                <x-mine.button type="button" @class([
                    "sm:pr-4!" => app()->isLocale('en'),
                    "sm:pl-4!" => app()->isLocale('fa'),
                    "mine-btn-primary px-3.5! sm:px-3!"
                ])>
                    <div class="flex justify-center items-center gap-2">
                        <x-mine.icon name="plus" @class([
                            "mb-1" => app()->isLocale('en'),
                            "size-4"
                        ]) variant="micro" />
                        <p class="text-sm font-medium hidden sm:inline"><span class="hidden sm:inline md:hidden">{{ __('New') }}</span><span class="hidden md:inline">{{ __('New plan') }}</span></p>
                    </div>
                </x-mine.button>
            </x-mine.modal.trigger>
            <x-mine.dropdown group="plan-filter">
                <x-mine.dropdown.trigger as="div">
                    <div @class([
                        "sm:pl-3!" => app()->isLocale('en'),
                        "sm:pr-3!" => app()->isLocale('fa'),
                        "w-full mine-btn-primary flex items-center h-11 px-5 sm:px-4 rounded-xl cursor-pointer select-none"
                    ])>
                        <x-mine.icon name="arrow-long-up" @class([
                            "-mr-1 -ml-2.5 sm:ml-0" => app()->isLocale('en'),
                            "-ml-1 -mr-2.5 sm:mr-0" => app()->isLocale('fa'),
                            "inline size-4"
                        ]) variant="micro" />
                        <x-mine.icon name="arrow-long-down" @class([
                            "-ml-1 -mr-2.5 sm:mr-0" => app()->isLocale('en'),
                            "-mr-1 -ml-2.5 sm:ml-0" => app()->isLocale('fa'),
                            "inline size-4"
                        ]) variant="micro" />
                        <p @class([
                            "sm:pl-1 pt-1" => app()->isLocale('en'),
                            "sm:pr-1" => app()->isLocale('fa'),
                            "text-sm font-medium hidden sm:inline"
                        ])><span class="hidden sm:inline md:hidden">{{ __('Sort') }}</span><span class="hidden md:inline">{{ __('Sort by') }}</span></p>
                    </div>
                </x-mine.dropdown.trigger>
                <x-mine.dropdown.content class="mt-1!" placement="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}">
                    @foreach ([
                        'state' => __('State'),
                        'deadline' => __('Deadline'),
                        'load' => __('Load'),
                        'latest' => __('Date created'),
                        'name' => __('Plan name'),
                    ] as $value => $label)
                        <x-mine.dropdown.item>
                            <div class="w-full rounded-lg py-2 px-4 flex items-center hover:cursor-pointer {{ $sort === $value ? 'bg-(--mine-dropdown-item-bg-hover)' : '' }}"
                                @click="$wire.$island('plans-content').$set('sort', '{{ $value }}')">
                                <p class="text-sm font-medium {{ app()->isLocale('en') ? 'pt-1' : '' }} {{ $sort === $value ? 'mine-text-link' : 'mine-text-secondary' }}">{{ $label }}</p>
                            </div>
                        </x-mine.dropdown.item>
                    @endforeach
                </x-mine.dropdown.content>
            </x-mine.dropdown>
        </div>
        <div class="w-full md:w-auto flex justify-between gap-2">
            <x-mine.dropdown group="plan-filter" class="w-full md:w-auto">
                <x-mine.dropdown.trigger as="div" class="w-full md:w-auto">
                    <div @class([
                        "sm:pr-4!" => app()->isLocale('en'),
                        "sm:pl-4!" => app()->isLocale('fa'),
                        "w-full md:w-auto mine-btn-primary flex items-center justify-center gap-2 h-11 px-3.5 rounded-xl cursor-pointer select-none"
                    ])>
                        <x-mine.icon name="funnel" @class([
                            "size-4"
                        ]) variant="micro" />
                        <p @class([
                            "pt-1" => app()->isLocale('en'),
                            "text-sm font-medium"
                        ])>
                            {{ __('Filter plan') }}
                        </p>
                    </div>
                </x-mine.dropdown.trigger>
                <x-mine.dropdown.content class="mt-1!" placement="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}">
                    @foreach ([
                        'all' => __('All'),
                        'active' => __('Active'),
                        'completed' => __('Completed'),
                        'overdue' => __('Overdue'),
                    ] as $value => $label)
                        <x-mine.dropdown.item>
                            <div class="w-full rounded-lg py-2 px-4 flex items-center hover:cursor-pointer {{ $status_filter === $value ? 'bg-(--mine-dropdown-item-bg-hover)' : '' }}"
                                @click="$wire.$island('plans-content').$set('status_filter', '{{ $value }}')">
                                <p class="text-sm font-medium {{ app()->isLocale('en') ? 'pt-1' : '' }} {{ $status_filter === $value ? 'mine-text-link' : 'mine-text-secondary' }}">{{ $label }}</p>
                            </div>
                        </x-mine.dropdown.item>
                    @endforeach
                </x-mine.dropdown.content>
            </x-mine.dropdown>
            <x-mine.dropdown group="plan-filter" class="md:hidden w-full">
                <x-mine.dropdown.trigger as="div" class=" w-full">
                    <div @class([
                        "sm:pr-4!" => app()->isLocale('en'),
                        "sm:pl-4!" => app()->isLocale('fa'),
                        "w-full mine-btn-primary flex items-center justify-center gap-2 h-11 px-3.5 rounded-xl cursor-pointer select-none"
                    ])>
                        <x-mine.icon name="calendar" @class([
                            "size-4"
                        ]) variant="micro" />
                        <p @class([
                            "pt-1" => app()->isLocale('en'),
                            "text-sm font-medium"
                        ])>
                            {{ __('Date range') }}
                        </p>
                    </div>
                </x-mine.dropdown.trigger>
                <x-mine.dropdown.content class="mt-1! flex justify-center" placement="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}">
                    <div class="flex justify-center items-center w-80">
                        <x-mine.calendar wire:model.live="range_filter" island="plans-content" :card="false" />
                    </div>
                </x-mine.dropdown.content>
            </x-mine.dropdown>
        </div>
    </div>
    <div class="md:flex md:gap-6">
        <div class="relative flex-1">
            <div wire:loading.delay.short class="absolute inset-0 z-10">
                <div class="flex h-full w-full items-center justify-center">
                    <svg class="size-8 animate-spin mine-text-secondary" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" role="status" aria-label="Loading">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
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
                                    'icon' => 'trophy',
                                    'header_class' => 'mine-badge-secondary',
                                    'title_class' => 'line-through',
                                    'status_label' => __('Completed'),
                                    'separator' => 'secondary',
                                    'card' => 'mine-card-interactive mine-card-secondary',
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
                                    'icon' => 'bell-alert',
                                    'header_class' => 'mine-badge-danger',
                                    'title_class' => '',
                                    'status_label' => __('Overdue'),
                                    'separator' => 'danger',
                                    'card' => 'mine-card-interactive mine-card-danger',
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
                                    'icon' => 'rocket-launch',
                                    'header_class' => 'mine-badge-primary',
                                    'title_class' => '',
                                    'status_label' => __('Active'),
                                    'separator' => 'primary',
                                    'card' => 'mine-card-interactive',
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

                        <div wire:key="plan-{{ $plan->id }}"
                            @class([
                                $config['border-l'] => app()->isLocale('en'),
                                $config['border-r'] => app()->isLocale('fa'),
                            "self-start {$config['card']} flex flex-col px-4 sm:px-6 md:px-8 py-4 sm:pb-5 md:pb-6 sm:pt-6 md:pt-8"
                            ])>
                            <div class="flex justify-between gap-4 mb-8 min-w-0">
                                <div class="flex min-w-0 flex-1">
                                    <div @class([
                                        "mr-4" => app()->isLocale('en'),
                                        "ml-4" => app()->isLocale('fa'),
                                        "shrink-0 " . $config['header_class'] . " rounded-xl size-16 flex justify-center items-center"
                                    ])>
                                        <x-mine.icon :name="$config['icon']" class="size-8" variant="mini" />
                                    </div>
                                    <div class="flex flex-col justify-between gap-2 min-w-0">
                                        <div class="flex items-center gap-4 min-w-0">
                                            <h1 @class([
                                                "pt-1" => app()->isLocale('en'),
                                                "text-base font-semibold mine-text-primary truncate min-w-0 " . $config['title_class']
                                            ])>{{ $plan->name }}</h1>
                                            <div class="{{ $config['header_class'] }} rounded-xl h-7 flex justify-center items-center px-3 shrink-0">
                                                <p @class([
                                                    "pt-1" => app()->isLocale('en'),
                                                    "text-[13px] font-semibold"
                                                ])>{{ $config['status_label'] }}</p>
                                            </div>
                                        </div>
                                        <p class="text-sm font-medium mine-text-secondary min-w-0 line-clamp-2 {{ $config['title_class'] }}">
                                            {{ $plan->description ?: __('No description') }}</p>
                                    </div>
                                </div>
                                <div class="flex justify-end items-start h-full shrink-0">
                                    <x-mine.dropdown group="plan-actions">
                                        <x-mine.dropdown.trigger>
                                            <div class="mine-btn-icon p-2 rounded-xl">
                                                <x-mine.icon name="ellipsis-horizontal" class="size-5" variant="mini" />
                                            </div>
                                        </x-mine.dropdown.trigger>
                                        <x-mine.dropdown.content placement="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}">
                                            <x-mine.dropdown.item href="{{ route('tasks', ['plan_filter' => [$plan->id]]) }}">
                                                <div class="w-full py-2 px-4 flex items-center mine-text-link">
                                                    <p class="text-sm font-medium">{{ __('View tasks') }}</p>
                                                </div>
                                            </x-mine.dropdown.item>
                                            <x-mine.dropdown.divider class="-mx-1" />
                                            <x-mine.dropdown.item>
                                                <div class="w-full py-2 px-4 flex items-center mine-text-link hover:cursor-pointer"
                                                    @click.stop="$wire.set('editing_id', {{ $plan->id }}, false);
                                                            $wire.set('edit_name', @js($plan->name), false);
                                                            $wire.set('edit_description', @js($plan->description), false);
                                                            $wire.set('edit_range', @js(['start' => $plan->start_date?->format('Y-m-d'), 'end' => $plan->finish_date?->format('Y-m-d')]), false);
                                                            $dispatch('open-modal', { id: 'edit-plan-form' })">
                                                    <p @class([
                                                        "pt-0.5" => app()->isLocale('en'),
                                                        "text-sm font-medium"
                                                    ])>{{ __('Edit') }}</p>
                                                </div>
                                            </x-mine.dropdown.item>
                                            <x-mine.dropdown.item destructive>
                                                <div class="w-full py-2 px-4 flex items-center mine-text-error hover:cursor-pointer"
                                                    @click.stop="$wire.set('deleting_id', {{ $plan->id }}, false);
                                                            $dispatch('open-modal', { id: 'delete-plan-confirmation' })">
                                                    <p @class([
                                                        "pt-0.5" => app()->isLocale('en'),
                                                        "text-sm font-medium"
                                                    ])>{{ __('Delete') }}</p>
                                                </div>
                                            </x-mine.dropdown.item>
                                        </x-mine.dropdown.content>
                                    </x-mine.dropdown>
                                </div>
                            </div>
                            <div class="flex flex-wrap justify-between items-center gap-x-6 gap-y-4 mb-4">
                                @if($config['days'])
                                    <div class="flex items-center">
                                        <div @class([
                                            "pl-3 pr-4" => app()->isLocale('en'),
                                            "pr-3 pl-4" => app()->isLocale('fa'),
                                            $config['days']['badge'] . " rounded-xl h-9 flex justify-center items-center gap-2"
                                        ])>
                                            <x-mine.icon name="clock" variant="micro" class="" />
                                            <p @class([
                                                "pt-1" => app()->isLocale('en'),
                                                "text-[13px] font-medium"
                                            ])>{{ app()->isLocale('fa') ? App\Support\PersianNumber::show($config['days']['value']) : $config['days']['value'] }} {{ $config['days']['label'] }}</p>
                                        </div>
                                    </div>
                                @endif
                                <div class="flex items-center min-w-0">
                                    <div @class([
                                        "pl-3 pr-4" => app()->isLocale('en'),
                                        "pr-3 pl-4" => app()->isLocale('fa'),
                                        $config['header_class'] . " rounded-xl h-9 flex justify-center items-center shrink-0"
                                    ])>
                                        <x-mine.icon name="calendar" variant="micro" />
                                        <p @class([
                                            "pt-1 ml-2" => app()->isLocale('en'),
                                            "mr-2" => app()->isLocale('fa'),
                                            "text-[13px] font-medium"
                                        ])>{{ app()->isLocale('fa')
                                            ? \App\Support\Jalali::format($plan->start_date, 'd MMM ، y')
                                            : $plan->start_date->format('d M , Y') }}</p>
                                        <div class="flex justify-center items-center mx-4">
                                            <x-mine.icon name="arrow-long-{{ app()->isLocale('en') ? 'right' : 'left' }}" variant="mini" />
                                        </div>
                                        <p @class([
                                            "pt-1" => app()->isLocale('en'),
                                            "text-[13px] font-medium"
                                        ])>{{ app()->isLocale('fa')
                                            ? \App\Support\Jalali::format($plan->finish_date, 'd MMM ، y')
                                            : $plan->finish_date->format('d M , Y') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4 opacity-40">
                                <x-mine.separator :variant="$config['separator']" />
                            </div>
                            <div class="flex mb-4">
                                <div class="flex flex-col justify-between flex-3">
                                    <h2 class="text-[13px] font-medium mine-text-secondary mb-2">{{ __('Progress') }}</h2>
                                    <h2 class="text-2xl font-bold {{ $config['percent_color'] }} mb-2">
                                        {{ app()->isLocale('fa') ? App\Support\PersianNumber::show($config['pct']) : $config['pct'] }}%
                                    </h2>
                                    <x-mine.progress total="{{ $config['progress_total'] }}"
                                        progress="{{ $config['progress_value'] }}"
                                        variant="{{ $config['progress_variant'] ?? 'primary' }}"
                                        class="h-3" />
                                </div>
                                <div class="flex items-center justify-center flex-1">
                                </div>
                            </div>
                            <div class="mb-4 opacity-40">
                                <x-mine.separator :variant="$config['separator']" />
                            </div>
                            <div class="flex gap-4 justify-between items-center">
                                @if($config['action'] === 'reopen')
                                    <x-mine.button type="button" class="mine-btn-secondary"
                                        @click.stop="$wire.set('reopening_id', {{ $plan->id }}, false); $dispatch('open-modal', { id: 'reopen-plan-confirmation' })">
                                        <div class="flex justify-center items-center gap-1.5 text-sm font-medium">
                                            <x-mine.icon name="arrow-path" variant="micro" class="size-4" />
                                            <p>{{ __('Reopen') }}</p>
                                        </div>
                                    </x-mine.button>
                                @else
                                    <a href="{{ route('tasks', ['plan_filter' => [$plan->id], 'add_plan' => $plan->id]) }}" wire:navigate.hover class="w-full">
                                        <x-mine.button type="button" class="{{ $config['add_class'] }}">
                                            <div class="flex justify-center items-center gap-1 text-sm font-md">
                                                <x-mine.icon name="plus" variant="micro" class="size-4" />
                                                <p>{{ __('Add task') }}</p>
                                            </div>
                                        </x-mine.button>
                                    </a>
                                    <x-mine.button type="button" class="{{ $config['complete_class'] }}"
                                        @click.stop="$wire.set('completing_id', {{ $plan->id }}, false); $dispatch('open-modal', { id: 'complete-plan-confirmation' })">
                                        <div class="flex justify-center items-center gap-1 text-sm font-medium">
                                            <x-mine.icon name="check" variant="micro" class="size-4" />
                                            <p class="hidden sm:block">{{ __('Mark as completed') }}</p>
                                            <p class="sm:hidden">{{ __('Complete') }}</p>
                                        </div>
                                    </x-mine.button>
                                @endif
                            </div>
                        </div>
                    @empty
                        @if($this->hasActiveFilters)
                            <div class="col-span-full text-center py-12">
                                <x-mine.icon name="magnifying-glass" class="size-10 mx-auto mb-3 mine-text-secondary" />
                                <p class="mine-text-secondary text-sm font-medium">{{ __('No plans found') }}</p>
                            </div>
                        @else
                            <div class="col-span-full text-center py-12">
                                <x-mine.icon name="rocket-launch" class="size-10 mx-auto mb-3 mine-text-secondary" />
                                <p class="mine-text-secondary text-sm font-medium">{{ __('No plans yet') }}</p>
                            </div>
                        @endif
                    @endforelse
                </div>
            </div>
        </div>
        <div class="hidden md:flex justify-center w-80">
            <x-mine.calendar wire:model.live="range_filter" island="plans-content" />
        </div>
    </div>
    <div class="mt-6 w-full">
        {{ $this->plans->links(data: ['scrollTo' => false]) }}
    </div>
    @endisland

    <x-mine.modal id="add-plan-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base font-semibold mine-text-primary">{{ __('Add new plan') }}</h2>
                <button type="button" @click="close(); $wire.cancelAdd()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" variant="micro" />
                </button>
            </div>

            @if($add_error === 'already_exists')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="{{ __('Duplicate plan!') }}">{{ __('A plan with this name already exists.') }}</x-mine.alert>
                </div>
            @endif

            @if($add_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="{{ __('Too many create-plan attempts!') }}">{{ __('Please try again in a minute.') }}</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="addPlan">
                <div>
                    <x-mine.input label="{{ __('Plan name') }}" wire:model="add_name" placeholder="{{ __('Enter plan name') }}" />
                </div>
                <div>
                    <x-mine.datepicker mode="range" position="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}" wire:model="add_range" label="{{ __('Date range') }}" />
                </div>
                <div>
                    <x-mine.textarea label="{{ __('Plan description') }}" wire:model="add_description"
                        placeholder="{{ __('Enter plan description') }}" />
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelAdd()" class="mine-btn-ghost">
                        <p class="text-sm">
                            {{ __('Cancel') }}
                        </p>
                    </x-mine.button>
                    <x-mine.button wire:target="addPlan" class="mine-btn-primary">
                        <p class="text-sm">
                            {{ __('Create') }}
                        </p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="edit-plan-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base font-semibold mine-text-primary">{{ __('Edit plan') }}</h2>
                <button type="button" @click="close(); $wire.cancelEdit()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" variant="micro" />
                </button>
            </div>

            @if($edit_error === 'already_exists')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="{{ __('Duplicate plan!') }}">{{ __('A plan with this name already exists.') }}</x-mine.alert>
                </div>
            @endif

            @if($edit_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="{{ __('Too many edit-plan attempts!') }}">{{ __('Please try again in a minute.') }}</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="editPlan">
                <div>
                    <x-mine.input label="{{ __('Plan name') }}" wire:model="edit_name" placeholder="{{ __('Enter plan name') }}" />
                </div>
                <div>
                    <x-mine.datepicker mode="range" position="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}" wire:model="edit_range" label="{{ __('Date range') }}" />
                </div>
                <div>
                    <x-mine.textarea label="{{ __('Plan description') }}" wire:model="edit_description"
                        placeholder="{{ __('Enter plan description') }}" />
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelEdit()" class="mine-btn-ghost">
                        <p class="text-sm">
                            {{ __('Cancel') }}
                        </p>
                    </x-mine.button>
                    <x-mine.button wire:target="editPlan" class="mine-btn-primary">
                        <p class="text-sm">
                            {{ __('Save') }}
                        </p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="delete-plan-confirmation" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base font-semibold mine-text-primary">{{ __('Delete plan') }}</h2>
                <button type="button" @click="close(); $wire.cancelDelete()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" variant="micro" />
                </button>
            </div>

            @if($delete_error === 'has_tasks')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="{{ __('Cannot delete plan!') }}">{{ __('This plan has tasks, reassign or delete them first.') }}</x-mine.alert>
                </div>
            @else
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="{{ __('Are you sure?') }}">{{ __('This action cannot be undone.') }}</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="deletePlan">
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelDelete()" class="mine-btn-ghost">
                        <p class="text-sm">
                            {{ __('Cancel') }}
                        </p>
                    </x-mine.button>
                    <x-mine.button wire:target="deletePlan" class="mine-btn-danger">
                        <p class="text-sm">
                            {{ __('Delete') }}
                        </p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="complete-plan-confirmation" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base font-semibold mine-text-primary">{{ __('Complete plan') }}</h2>
                <button type="button" @click="close(); $wire.cancelComplete()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" variant="micro" />
                </button>
            </div>

            @if($complete_error === 'has_undone_tasks')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="{{ __('Cannot complete plan!') }}">{{ __('This plan still has unfinished tasks, complete or remove them first.') }}</x-mine.alert>
                </div>
            @else
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="{{ __('Are you sure?') }}">{{ __('This will mark the plan as completed.') }}</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="completePlan">
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelComplete()" class="mine-btn-ghost">
                        <p class="text-sm">
                            {{ __('Cancel') }}
                        </p>
                    </x-mine.button>
                    <x-mine.button wire:target="completePlan" class="mine-btn-primary">
                        <p class="text-sm">
                            {{ __('Complete') }}
                        </p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="reopen-plan-confirmation" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base font-semibold mine-text-primary">{{ __('Reopen plan') }}</h2>
                <button type="button" @click="close(); $wire.cancelReopen()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" variant="micro" />
                </button>
            </div>

            <div class="mb-4">
                <x-mine.alert variant="warning" title="{{ __('Are you sure?') }}">{{ __('This plan will be moved back to active.') }}</x-mine.alert>
            </div>

            <form class="flex flex-col gap-4" wire:submit="reopenPlan">
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelReopen()" class="mine-btn-ghost">
                        <p class="text-sm">
                            {{ __('Cancel') }}
                        </p>
                    </x-mine.button>
                    <x-mine.button wire:target="reopenPlan" class="mine-btn-primary">
                        <p class="text-sm">
                            {{ __('Reopen') }}
                        </p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>
</div>