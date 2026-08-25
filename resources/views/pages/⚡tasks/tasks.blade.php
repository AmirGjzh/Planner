<div class="flex-1 px-4 sm:px-8 md:px-16 pb-6 pt-4 flex flex-col">
    <div class="mb-6">
        <h1 class="font-bold text-base mine-text-primary">{{ __('My tasks') }}</h1>
    </div>
    @island(name: 'tasks-content', always: true)
    <div class="flex flex-col gap-2 sm:gap-4 mb-6">
        <div class="w-full flex items-center justify-between gap-2 sm:gap-4">
            <div class="w-full">
                <x-mine.input wire:model.live.debounce.200ms="search" placeholder="{{ __('Search tasks...') }}"
                    leftIcon="magnifying-glass" />
            </div>
            <x-mine.modal.trigger id="add-task-form">
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
                        <p class="text-sm font-medium hidden sm:inline"><span class="hidden sm:inline md:hidden">{{ __('New') }}</span><span class="hidden md:inline">{{ __('New task') }}</span></p>
                    </div>
                </x-mine.button>
            </x-mine.modal.trigger>
            <x-mine.dropdown group="task-filter">
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
                        'load' => __('Load'),
                        'latest' => __('Date created'),
                        'date' => __('Date'),
                        'priority' => __('Priority'),
                    ] as $value => $label)
                        <x-mine.dropdown.item>
                            <div class="w-full rounded-lg py-2 px-4 flex items-center hover:cursor-pointer {{ $sort === $value ? 'bg-(--mine-dropdown-item-bg-hover)' : '' }}"
                                @click="$wire.$island('tasks-content').$set('sort', '{{ $value }}')">
                                <p class="text-sm font-medium {{ app()->isLocale('en') ? 'pt-1' : '' }} {{ $sort === $value ? 'mine-text-link' : 'mine-text-secondary' }}">{{ $label }}</p>
                            </div>
                        </x-mine.dropdown.item>
                    @endforeach
                </x-mine.dropdown.content>
            </x-mine.dropdown>
            <x-mine.dropdown group="task-filter" class="w-full md:w-auto hidden md:flex">
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
                            {{ __('Filter task') }}
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
                                @click="$wire.$island('tasks-content').$set('status_filter', '{{ $value }}')">
                                <p class="text-sm font-medium {{ app()->isLocale('en') ? 'pt-1' : '' }} {{ $status_filter === $value ? 'mine-text-link' : 'mine-text-secondary' }}">{{ $label }}</p>
                            </div>
                        </x-mine.dropdown.item>
                    @endforeach
                </x-mine.dropdown.content>
            </x-mine.dropdown>
        </div>
        <div class="w-full md:w-auto flex justify-between gap-2 sm:gap-4">
            <x-mine.dropdown group="task-filter" class="w-full md:w-auto md:hidden">
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
                            "text-sm font-medium hidden sm:block"
                        ])>
                            {{ __('Filter task') }}
                        </p>
                    </div>
                </x-mine.dropdown.trigger>
                <x-mine.dropdown.content class="mt-1!" placement="bottom-{{ app()->isLocale('fa') ? 'end' : 'start' }}">
                    @foreach ([
                        'all' => __('All'),
                        'active' => __('Active'),
                        'completed' => __('Completed'),
                        'overdue' => __('Overdue'),
                    ] as $value => $label)
                        <x-mine.dropdown.item>
                            <div class="w-full rounded-lg py-2 px-4 flex items-center hover:cursor-pointer {{ $status_filter === $value ? 'bg-(--mine-dropdown-item-bg-hover)' : '' }}"
                                @click="$wire.$island('tasks-content').$set('status_filter', '{{ $value }}')">
                                <p class="text-sm font-medium {{ app()->isLocale('en') ? 'pt-1' : '' }} {{ $status_filter === $value ? 'mine-text-link' : 'mine-text-secondary' }}">{{ $label }}</p>
                            </div>
                        </x-mine.dropdown.item>
                    @endforeach
                </x-mine.dropdown.content>
            </x-mine.dropdown>
            <x-mine.dropdown group="task-filter" multiple model="category_filter" island="tasks-content" class="w-full">
                <x-mine.dropdown.trigger as="div" class="w-full">
                    <div @class([
                        "sm:pr-4!" => app()->isLocale('en'),
                        "sm:pl-4!" => app()->isLocale('fa'),
                        "w-full mine-btn-primary flex items-center justify-center gap-2 h-11 px-3.5 rounded-xl cursor-pointer select-none"
                    ])>
                        <x-mine.icon name="folder" @class([
                            "size-4"
                        ]) variant="micro" />
                        <p @class([
                            "pt-1" => app()->isLocale('en'),
                            "text-sm font-medium hidden sm:block"
                        ])>
                            {{ __('Category') }}
                        </p>
                        @if(count($category_filter) > 0)
                            <span class="mine-badge-primary text-xs font-semibold rounded-full px-1.5 pb-0.5 pt-1">
                            {{ app()->isLocale('en') ? count($category_filter) : App\Support\PersianNumber::show(count($category_filter)) }}
                            </span>
                        @endif
                    </div>
                </x-mine.dropdown.trigger>
                <x-mine.dropdown.content class="mt-1! max-h-60 overflow-y-auto mine-scrollbar" placement="bottom-{{ app()->isLocale('fa') ? 'end' : 'start' }}">
                    @foreach ($this->categories as $category)
                        <x-mine.dropdown.item :value="$category->id">
                            <p @class([
                                "pr-4 pl-3" => app()->isLocale('en'),
                                "pl-4 pr-3" => app()->isLocale('fa'),
                                "w-full flex py-2 text-sm font-medium mine-text-primary cursor-pointer"
                            ])>
                                {{ $category->name }}
                            </p>
                        </x-mine.dropdown.item>
                    @endforeach
                </x-mine.dropdown.content>
            </x-mine.dropdown>
            <x-mine.dropdown group="task-filter" multiple model="plan_filter" island="tasks-content" class="w-full">
                <x-mine.dropdown.trigger as="div" class="w-full">
                    <div @class([
                        "sm:pr-4!" => app()->isLocale('en'),
                        "sm:pl-4!" => app()->isLocale('fa'),
                        "w-full mine-btn-primary flex items-center justify-center gap-2 h-11 px-3.5 rounded-xl cursor-pointer select-none"
                    ])>
                        <x-mine.icon name="rocket-launch" @class([
                            "size-4"
                        ]) variant="micro" />
                        <p @class([
                            "pt-1" => app()->isLocale('en'),
                            "text-sm font-medium hidden sm:block"
                        ])>
                            {{ __('Plan') }}
                        </p>
                        @if(count($plan_filter) > 0)
                            <span class="mine-badge-primary text-xs font-semibold rounded-full px-1.5 pb-0.5 pt-1">
                            {{ app()->isLocale('en') ? count($plan_filter) : App\Support\PersianNumber::show(count($plan_filter)) }}
                            </span>
                        @endif
                    </div>
                </x-mine.dropdown.trigger>
                <x-mine.dropdown.content class="mt-1! max-h-60 overflow-y-auto mine-scrollbar" placement="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}">
                    @foreach ($this->plans as $plan)
                        <x-mine.dropdown.item :value="$plan->id">
                            <p @class([
                                "pr-4 pl-3" => app()->isLocale('en'),
                                "pl-4 pr-3" => app()->isLocale('fa'),
                                "w-full flex py-2 text-sm font-medium mine-text-primary cursor-pointer"
                            ])>
                                {{ $plan->name }}
                            </p>
                        </x-mine.dropdown.item>
                    @endforeach
                </x-mine.dropdown.content>
            </x-mine.dropdown>
            <x-mine.dropdown group="task-filter" class="md:hidden w-full">
                <x-mine.dropdown.trigger as="div" class="w-full">
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
                            "text-sm font-medium hidden sm:block"
                        ])>
                            {{ __('Date range') }}
                        </p>
                    </div>
                </x-mine.dropdown.trigger>
                <x-mine.dropdown.content class="mt-1! flex justify-center" placement="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}">
                    <div class="flex justify-center items-center w-80">
                        <x-mine.calendar wire:model.live="range_filter" island="tasks-content" :card="false" />
                    </div>
                </x-mine.dropdown.content>
            </x-mine.dropdown>
        </div>
    </div>
    <div class="md:flex md:gap-6">
        <div class="relative flex-1">
            <div wire:loading.delay.short class="absolute inset-0 z-10">
                <div class="flex h-full w-full items-center justify-center">
                    <svg class="size-8 animate-spin mine-text-secondary" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" role="status" aria-label="Loading">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                </div>
            </div>
            <div wire:loading.delay.short.class="opacity-40" class="transition-opacity">
                <div class="grid grid-cols-1 gap-4">
                    @forelse ($this->tasks as $task)
                        @php
                            $status = $task->done ? 'completed' : ($task->task_date->isPast() ? 'overdue' : 'active');

                            $config = match ($status) {
                                'completed' => [
                                    'icon' => 'clipboard-document-check',
                                    'header_class' => 'mine-badge-secondary',
                                    'title_class' => 'line-through',
                                    'status_label' => __('Completed'),
                                    'separator' => 'secondary',
                                    'card' => 'mine-card-interactive mine-card-secondary',
                                    'border-l' => 'border-l-6 border-l-(--mine-btn-secondary-bg) hover:border-l-(--mine-btn-secondary-bg-hover)',
                                    'border-r' => 'border-r-6 border-r-(--mine-btn-secondary-bg) hover:border-r-(--mine-btn-secondary-bg-hover)',
                                    'action_button' => 'mine-btn-secondary',
                                ],
                                'overdue' => [
                                    'icon' => 'bell-alert',
                                    'header_class' => 'mine-badge-danger',
                                    'title_class' => '',
                                    'status_label' => __('Overdue'),
                                    'separator' => 'danger',
                                    'card' => 'mine-card-interactive mine-card-danger',
                                    'border-l' => 'border-l-6 border-l-(--mine-btn-danger-bg) hover:border-l-(--mine-btn-danger-bg-hover)',
                                    'border-r' => 'border-r-6 border-r-(--mine-btn-danger-bg) hover:border-r-(--mine-btn-danger-bg-hover)',
                                    'action_button' => 'mine-btn-danger',
                                ],
                                default => [
                                    'icon' => 'clipboard-document-list',
                                    'header_class' => 'mine-badge-primary',
                                    'title_class' => '',
                                    'status_label' => __('Active'),
                                    'separator' => 'primary',
                                    'card' => 'mine-card-interactive',
                                    'border-l' => 'border-l-6 border-l-(--mine-btn-primary-bg) hover:border-l-(--mine-btn-primary-bg-hover)',
                                    'border-r' => 'border-r-6 border-r-(--mine-btn-primary-bg) hover:border-r-(--mine-btn-primary-bg-hover)',
                                    'action_button' => 'mine-btn-primary',
                                ],
                            };

                            $priorityLabel = match ($task->priority->value) {
                                'low' => __('Low'),
                                'medium' => __('Medium'),
                                'high' => __('High'),
                            };
                        @endphp

                        <div wire:key="task-{{ $task->id }}"
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
                                            ])>
                                                {{ $task->title }}
                                            </h1>
                                            <div
                                                class="{{ $config['header_class'] }} rounded-xl h-7 flex justify-center items-center px-3 shrink-0">
                                                <p @class([
                                                    "pt-1" => app()->isLocale('en'),
                                                    "text-[13px] font-semibold"
                                                ])>{{ $config['status_label'] }}</p>
                                            </div>
                                        </div>
                                        <p class="text-sm font-medium mine-text-secondary line-clamp-2 min-w-0 {{ $config['title_class'] }}">
                                            {{ $task->description ?: __('No description') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex justify-end items-start h-full shrink-0">
                                    <x-mine.dropdown group="task-actions">
                                        <x-mine.dropdown.trigger>
                                            <div class="mine-btn-icon p-2 rounded-xl">
                                                <x-mine.icon name="ellipsis-horizontal" class="size-5" variant="mini" />
                                            </div>
                                        </x-mine.dropdown.trigger>
                                        <x-mine.dropdown.content placement="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}">
                                            <x-mine.dropdown.item>
                                                <div class="w-full py-2 px-4 flex items-center mine-text-link hover:cursor-pointer"
                                                    @click.stop="$wire.set('editing_id', {{ $task->id }}, false);
                                                    $wire.set('edit_title', @js($task->title), false);
                                                    $wire.set('edit_description', @js($task->description), false);
                                                    $wire.set('edit_date', @js($task->task_date->format('Y-m-d')), false);
                                                    $wire.set('edit_estimated_minutes', {{ $task->estimated_minutes }}, false);
                                                    $wire.set('edit_alarm_days', {{ $task->day_before_alarm }}, false);
                                                    $wire.set('edit_priority', @js($task->priority->value), false);
                                                    $wire.set('edit_category_id', {{ $task->category_id }}, false);
                                                    $wire.set('edit_plan_id', @js($task->plan_id), false);
                                                    $dispatch('open-modal', { id: 'edit-task-form' })"
                                                >
                                                    <p @class([
                                                        "pt-0.5" => app()->isLocale('en'),
                                                        "text-sm font-medium"
                                                    ])>{{ __('Edit') }}</p>
                                                </div>
                                            </x-mine.dropdown.item>
                                            <x-mine.dropdown.item destructive>
                                                <div class="w-full py-2 px-4 flex items-center mine-text-error hover:cursor-pointer"
                                                    @click.stop="$wire.set('deleting_id', {{ $task->id }}, false);
                                                    $dispatch('open-modal', { id: 'delete-task-confirmation' })">
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
                            <div class="flex flex-col justify-between">
                                <div class="flex flex-col gap-y-4 gap-x-2">
                                    <div class="flex gap-2">
                                        <div @class([
                                            "pl-3 pr-4" => app()->isLocale('en'),
                                            "pr-3 pl-4" => app()->isLocale('fa'),
                                            $config['header_class'] . " rounded-xl h-9 flex items-center gap-2 justify-center"
                                        ])>
                                            <x-mine.icon name="folder" variant="mini" />
                                            <p @class([
                                                "pt-1" => app()->isLocale('en'),
                                                "text-[13px] font-medium"
                                            ])>{{ $task->category->name }}</p>
                                        </div>
                                        @if ($task->plan)
                                            <div @class([
                                                "pl-3 pr-4" => app()->isLocale('en'),
                                                "pr-3 pl-4" => app()->isLocale('fa'),
                                                $config['header_class'] . " rounded-xl h-9 flex items-center gap-2 justify-center"
                                            ])>
                                                <x-mine.icon name="rocket-launch" variant="micro" />
                                                <p @class([
                                                    "pt-1" => app()->isLocale('en'),
                                                    "text-[13px] font-medium"
                                                ])>{{ $task->plan->name }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex gap-2 mb-4">
                                        <div @class([
                                            "pl-3 pr-4" => app()->isLocale('en'),
                                            "pr-3 pl-4" => app()->isLocale('fa'),
                                            $config['header_class'] . " rounded-xl h-9 flex items-center gap-2 justify-center"
                                        ])>
                                            <x-mine.icon name="calendar" variant="micro" />
                                            <p @class([
                                                "pt-1" => app()->isLocale('en'),
                                                "text-[13px] font-medium"
                                            ])>{{ app()->isLocale('fa')
                                                ? \App\Support\Jalali::format($task->task_date, 'd MMM ، y')
                                                : $task->task_date->format('d M , Y') }}</p>
                                        </div>
                                        <div @class([
                                            "pl-3 pr-4" => app()->isLocale('en'),
                                            "pr-3 pl-4" => app()->isLocale('fa'),
                                            $config['header_class'] . " rounded-xl h-9 flex items-center gap-2 justify-center"
                                        ])>
                                            <x-mine.icon name="clock" variant="micro"/>
                                            <p @class([
                                                "pt-1" => app()->isLocale('en'),
                                                "text-[13px] font-medium"
                                            ])>{{ app()->isLocale('fa') ? App\Support\PersianNumber::show($task->estimated_minutes) : $task->estimated_minutes }} {{ __('min') }}</p>
                                        </div>
                                        <div @class([
                                            "pl-3 pr-4" => app()->isLocale('en'),
                                            "pr-3 pl-4" => app()->isLocale('fa'),
                                            $config['header_class'] . " rounded-xl h-9 flex items-center gap-2 justify-center"
                                        ])>
                                            <x-mine.icon name="fire" variant="micro" />
                                            <p @class([
                                                "pt-1" => app()->isLocale('en'),
                                                "text-[13px] font-medium"
                                            ])>{{ $priorityLabel }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="opacity-40 mb-4">
                                    <x-mine.separator :variant="$config['separator']" />
                                </div>
                                <div class="flex items-end">
                                    @if ($status === 'completed')
                                        <x-mine.button type="button" class="{{ $config['action_button'] }}"
                                            @click.stop="$wire.set('reopening_id', {{ $task->id }}, false); $dispatch('open-modal', { id: 'reopen-task-confirmation' })">
                                            <div class="flex justify-center items-center gap-1.5 text-sm font-medium">
                                                <x-mine.icon name="arrow-path" variant="micro" class="size-4" />
                                                <p>{{ __('Reopen') }}</p>
                                            </div>
                                        </x-mine.button>
                                    @else
                                        <x-mine.button type="button" class="{{ $config['action_button'] }}"
                                            @click.stop="$wire.set('completing_id', {{ $task->id }}, false); $dispatch('open-modal', { id: 'complete-task-confirmation' })">
                                            <div class="flex justify-center items-center gap-1 text-sm font-medium">
                                                <x-mine.icon name="check" variant="micro" class="size-4" />
                                                <p class="hidden sm:block">{{ __('Mark as completed') }}</p>
                                                <p class="sm:hidden">{{ __('Complete') }}</p>
                                            </div>
                                        </x-mine.button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        @if($this->hasActiveFilters)
                            <div class="col-span-full text-center py-12">
                                <x-mine.icon name="magnifying-glass" class="size-10 mx-auto mb-3 mine-text-secondary" />
                                <p class="mine-text-secondary text-sm font-medium">{{ __('No tasks found') }}</p>
                            </div>
                        @else
                            <div class="col-span-full text-center py-12">
                                <x-mine.icon name="clipboard" class="size-10 mx-auto mb-3 mine-text-secondary" />
                                <p class="mine-text-secondary text-sm font-medium">{{ __('No tasks yet') }}</p>
                            </div>
                        @endif
                    @endforelse
                </div>
            </div>
        </div>
        <div class="hidden md:flex justify-center w-80">
            <x-mine.calendar wire:model.live="range_filter" island="tasks-content" />
        </div>
    </div>
    <div class="mt-6 w-full">
        {{ $this->tasks->links(data: ['scrollTo' => false]) }}
    </div>
    @endisland

    <x-mine.modal id="add-task-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base font-semibold mine-text-primary">{{ __('Add new task') }}</h2>
                <button type="button" @click="close(); $wire.cancelAdd()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" variant="micro" />
                </button>
            </div>

            @if($add_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="{{ __('Too many create-task attempts!') }}">{{ __('Please try again in a minute.') }}</x-mine.alert>
                </div>
            @endif

            @if($add_error === 'invalid_category')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="{{ __('Invalid category!') }}">{{ __('Please choose a valid category.') }}</x-mine.alert>
                </div>
            @endif

            @if($add_error === 'invalid_plan')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="{{ __('Invalid plan!') }}">{{ __('Please choose a valid plan.') }}</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="addTask">
                <div>
                    <x-mine.input label="{{ __('Task Title') }}" wire:model="add_title" placeholder="{{ __('Enter task title') }}" />
                </div>
                <div>
                    <x-mine.datepicker mode="single" position="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}" wire:model="add_date" label="{{ __('Task Date') }}" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <x-mine.input label="{{ __('Estimated Minutes') }}" wire:model="add_estimated_minutes" type="number" placeholder="{{ __('e.g. 30') }}" />
                    <x-mine.input label="{{ __('Days before Alarm') }}" wire:model="add_alarm_days" type="number" placeholder="{{ __('e.g. 1') }}" />
                </div>
                <x-mine.select label="{{ __('Priority') }}" wire:model="add_priority" placeholder="{{ __('Select priority') }}">
                    <x-mine.select.option value="low">{{ __('Low') }}</x-mine.select.option>
                    <x-mine.select.option value="medium">{{ __('Medium') }}</x-mine.select.option>
                    <x-mine.select.option value="high">{{ __('High') }}</x-mine.select.option>
                </x-mine.select>
                <x-mine.select label="{{ __('Category') }}" wire:model="add_category_id" placeholder="{{ __('Select category') }}" searchable>
                    @forelse ($this->categories as $category)
                        <x-mine.select.option value="{{ $category->id }}">{{ $category->name }}</x-mine.select.option>
                    @empty
                        <x-mine.select.option value="">{{ __('No categories yet') }}</x-mine.select.option>
                    @endforelse
                </x-mine.select>
                <x-mine.select label="{{ __('Plan') }}" wire:model="add_plan_id" placeholder="{{ __('Select plan') }}" searchable>
                    <x-mine.select.option value="">{{ __('No plan') }}</x-mine.select.option>
                    @foreach ($this->plans as $plan)
                        <x-mine.select.option value="{{ $plan->id }}">{{ $plan->name }}</x-mine.select.option>
                    @endforeach
                </x-mine.select>
                <div>
                    <x-mine.textarea label="{{ __('Description') }}" wire:model="add_description" placeholder="{{ __('Enter description') }}" />
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelAdd()" class="mine-btn-ghost">
                        <p class="text-sm">
                            {{ __('Cancel') }}
                        </p>
                    </x-mine.button>
                    <x-mine.button wire:target="addTask" class="mine-btn-primary">
                        <p class="text-sm">
                            {{ __('Create') }}
                        </p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="edit-task-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base font-semibold mine-text-primary">{{ __('Edit task') }}</h2>
                <button type="button" @click="close(); $wire.cancelEdit()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" variant="micro" />
                </button>
            </div>

            @if($edit_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="{{ __('Too many edit-task attempts!') }}">{{ __('Please try again in a minute.') }}</x-mine.alert>
                </div>
            @endif

            @if($edit_error === 'invalid_category')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="{{ __('Invalid category!') }}">{{ __('Please choose a valid category.') }}</x-mine.alert>
                </div>
            @endif

            @if($edit_error === 'invalid_plan')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="{{ __('Invalid plan!') }}">{{ __('Please choose a valid plan.') }}</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="editTask">
                <div>
                    <x-mine.input label="{{ __('Task Title') }}" wire:model="edit_title" placeholder="{{ __('Enter task title') }}" />
                </div>
                <div>
                    <x-mine.datepicker mode="single" position="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}" wire:model="edit_date" label="{{ __('Task Date') }}" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <x-mine.input label="{{ __('Estimated Minutes') }}" wire:model="edit_estimated_minutes" type="number" placeholder="{{ __('e.g. 30') }}" />
                    <x-mine.input label="{{ __('Days before Alarm') }}" wire:model="edit_alarm_days" type="number" placeholder="{{ __('e.g. 1') }}" />
                </div>
                <x-mine.select label="{{ __('Priority') }}" wire:model="edit_priority" placeholder="{{ __('Select priority') }}">
                    <x-mine.select.option value="low">{{ __('Low') }}</x-mine.select.option>
                    <x-mine.select.option value="medium">{{ __('Medium') }}</x-mine.select.option>
                    <x-mine.select.option value="high">{{ __('High') }}</x-mine.select.option>
                </x-mine.select>
                <x-mine.select label="{{ __('Category') }}" wire:model="edit_category_id" placeholder="{{ __('Select category') }}" searchable>
                    @forelse ($this->categories as $category)
                        <x-mine.select.option value="{{ $category->id }}">{{ $category->name }}</x-mine.select.option>
                    @empty
                        <x-mine.select.option value="">{{ __('No categories yet') }}</x-mine.select.option>
                    @endforelse
                </x-mine.select>
                <x-mine.select label="{{ __('Plan') }}" wire:model="edit_plan_id" placeholder="{{ __('Select plan') }}" searchable>
                    <x-mine.select.option value="">{{ __('No plan') }}</x-mine.select.option>
                    @foreach ($this->plans as $plan)
                        <x-mine.select.option value="{{ $plan->id }}">{{ $plan->name }}</x-mine.select.option>
                    @endforeach
                </x-mine.select>
                <div>
                    <x-mine.textarea label="{{ __('Description') }}" wire:model="edit_description"
                        placeholder="{{ __('Enter description') }}" />
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelEdit()" class="mine-btn-ghost">
                        <p class="text-sm">
                            {{ __('Cancel') }}
                        </p>
                    </x-mine.button>
                    <x-mine.button wire:target="editTask" class="mine-btn-primary">
                        <p class="text-sm">
                            {{ __('Save') }}
                        </p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="delete-task-confirmation" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base font-semibold mine-text-primary">{{ __('Delete task') }}</h2>
                <button type="button" @click="close(); $wire.cancelDelete()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" variant="micro" />
                </button>
            </div>

            <div class="mb-4">
                <x-mine.alert variant="warning" title="{{ __('Are you sure?') }}">{{ __('This action cannot be undone.') }}</x-mine.alert>
            </div>

            <form class="flex flex-col gap-4" wire:submit="deleteTask">
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelDelete()" class="mine-btn-ghost">
                        <p class="text-sm">
                            {{ __('Cancel') }}
                        </p>
                    </x-mine.button>
                    <x-mine.button wire:target="deleteTask" class="mine-btn-danger">
                        <p class="text-sm">
                            {{ __('Delete') }}
                        </p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="complete-task-confirmation" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base font-semibold mine-text-primary">{{ __('Complete task') }}</h2>
                <button type="button" @click="close(); $wire.cancelComplete()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" variant="micro" />
                </button>
            </div>

            @if($complete_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="{{ __('Too many complete-task attempts!') }}">{{ __('Please try again in a minute.') }}</x-mine.alert>
                </div>
            @else
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="{{ __('Are you sure?') }}">{{ __('This will mark the task as completed.') }}</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="completeTask">
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelComplete()" class="mine-btn-ghost">
                        <p class="text-sm">
                            {{ __('Cancel') }}
                        </p>
                    </x-mine.button>
                    <x-mine.button wire:target="completeTask" class="mine-btn-primary">
                        <p class="text-sm">
                            {{ __('Complete') }}
                        </p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="reopen-task-confirmation" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base font-semibold mine-text-primary">{{ __('Reopen task') }}</h2>
                <button type="button" @click="close(); $wire.cancelReopen()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" variant="micro" />
                </button>
            </div>

            @if($reopen_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="{{ __('Too many reopen-task attempts!') }}">{{ __('Please try again in a minute.') }}</x-mine.alert>
                </div>
            @else
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="{{ __('Are you sure?') }}">{{ __('This task will be moved back to active.') }}</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="reopenTask">
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelReopen()" class="mine-btn-ghost">
                        <p class="text-sm">
                            {{ __('Cancel') }}
                        </p>
                    </x-mine.button>
                    <x-mine.button wire:target="reopenTask" class="mine-btn-primary">
                        <p class="text-sm">
                            {{ __('Reopen') }}
                        </p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>
</div>