@php
    $today = now()->startOfDay();

    $levelLabel = fn (\App\Enums\WorkloadLevel $level): string => match ($level) {
        \App\Enums\WorkloadLevel::None => __('No tasks'),
        \App\Enums\WorkloadLevel::Light => __('Light'),
        \App\Enums\WorkloadLevel::Moderate => __('Moderate'),
        \App\Enums\WorkloadLevel::Heavy => __('Heavy'),
        \App\Enums\WorkloadLevel::VeryHeavy => __('Very heavy'),
    };

    $levelRange = fn (\App\Enums\WorkloadLevel $level): string => match ($level) {
        \App\Enums\WorkloadLevel::None => __('No tasks'),
        \App\Enums\WorkloadLevel::Light => __('Light (1–120 min)'),
        \App\Enums\WorkloadLevel::Moderate => __('Moderate (121–240 min)'),
        \App\Enums\WorkloadLevel::Heavy => __('Heavy (241–360 min)'),
        \App\Enums\WorkloadLevel::VeryHeavy => __('Very heavy (361+ min)'),
    };

    $workloadMeta = collect(\App\Enums\WorkloadLevel::cases())->mapWithKeys(fn ($level) => [
        $level->value => [
            'label' => $levelLabel($level),
            'range' => $levelRange($level),
            'dot' => match ($level) {
                \App\Enums\WorkloadLevel::None => 'bg-(--mine-workload-none-dot)',
                \App\Enums\WorkloadLevel::Light => 'bg-(--mine-workload-light-dot)',
                \App\Enums\WorkloadLevel::Moderate => 'bg-(--mine-workload-moderate-dot)',
                \App\Enums\WorkloadLevel::Heavy => 'bg-(--mine-workload-heavy-dot)',
                \App\Enums\WorkloadLevel::VeryHeavy => 'bg-(--mine-workload-very-heavy-dot)',
            },
            'text' => match ($level) {
                \App\Enums\WorkloadLevel::None => 'text-(--mine-workload-none-text)',
                \App\Enums\WorkloadLevel::Light => 'text-(--mine-workload-light-text)',
                \App\Enums\WorkloadLevel::Moderate => 'text-(--mine-workload-moderate-text)',
                \App\Enums\WorkloadLevel::Heavy => 'text-(--mine-workload-heavy-text)',
                \App\Enums\WorkloadLevel::VeryHeavy => 'text-(--mine-workload-very-heavy-text)',
            },
            'count' => match ($level) {
                \App\Enums\WorkloadLevel::Light => 1,
                \App\Enums\WorkloadLevel::Moderate => 2,
                \App\Enums\WorkloadLevel::Heavy => 3,
                \App\Enums\WorkloadLevel::VeryHeavy => 4,
                default => 0,
            },
        ],
    ])->all();

    $priorityLabel = fn (string $priority): string => match ($priority) {
        'low' => __('Low'),
        'medium' => __('Medium'),
        'high' => __('High'),
    };

    $arrowIcon = app()->isLocale('fa') ? 'ArrowLeft' : 'ArrowRight';
@endphp

<div class="flex-1 px-4 sm:px-8 md:px-16 pb-6 pt-4 flex flex-col">
    <div class="flex flex-col">
        <x-mine.animate class="flex items-center justify-between gap-4 mb-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="font-semibold text-base mine-text-primary mb-1">{{ __('Tasks needing attention') }}</h1>
                    <div class="rounded-full size-6 flex items-center justify-center mine-badge-primary text-xs font-bold mb-1 pt-0.5">
                        {{ count($this->upcomingTasks) }}
                    </div>
                </div>
                <p class="font-medium text-[13px] mine-text-secondary">{{ __('These tasks are due or their alarm time has been reached.') }}</p>
            </div>
            <a wire:navigate href="{{ route('tasks') }}"
                class="relative inline-flex items-center justify-center h-10 px-4 rounded-xl text-sm font-medium transition duration-200 ease-out cursor-pointer select-none whitespace-nowrap mine-btn-primary">
                <div class="flex items-center gap-2">
                    <p @class([
                        "pt-1" => app()->isLocale('en'),
                        "text-[13px] font-medium"
                    ])><span class="hidden md:inline">{{ __('View') }}</span> {{ __('All') }} <span
                            class="hidden sm:inline">{{ __('tasks') }}</span></p>
                    <x-mine.icon name="{{ $arrowIcon }}" size="16" weight="filled" />
                </div>
            </a>
        </x-mine.animate>
        <x-mine.horizontal-scroll scroller-class="flex gap-2 self-center px-2 pb-10 pt-2" class="px-0!">
            @forelse ($this->upcomingTasks as $task)
                @if ($task->task_date < $today)
                    <x-mine.animate wire:key="task-{{ $task->id }}" stagger="50" data-anim-index="{{ $loop->index }}"
                        class="min-w-50 mine-card mine-card-danger flex flex-col p-4">
                        <h2 class="text-sm font-semibold mine-text-primary mb-4">{{ $task->title }}</h2>
                        <div class="self-start mine-badge-danger rounded-xl h-7 flex items-center gap-1 px-2 mb-4">
                            <x-mine.icon name="Fire" size="16" weight="filled" />
                            <p class="font-medium text-xs">{{ $priorityLabel($task->priority->value) }}</p>
                        </div>
                        <div class="self-start mine-badge-danger rounded-xl h-7 flex items-center gap-1 px-2 mb-4">
                            <x-mine.icon name="Calendar" size="16" weight="filled" />
                            <p class="font-semibold text-xs">{{ __('Overdue') }}</p>
                            @php $daysAgo = (int) $task->task_date->startOfDay()->diffInDays($today); @endphp
                            <p class="font-medium text-xs">{{ $daysAgo === 1 ? __('1 day ago') : __(':count days ago', ['count' => $daysAgo]) }}</p>
                        </div>
                        <a wire:navigate href="{{ route('tasks', ['search' => $task->title]) }}"
                            class="relative inline-flex items-center justify-center h-9 px-4 rounded-xl text-sm font-semibold transition duration-200 ease-out cursor-pointer select-none whitespace-nowrap mine-btn-danger">
                            <div class="flex items-center gap-2">
                                <p @class([
                                    "pt-1" => app()->isLocale('en'),
                                    "text-[13px] font-medium"
                                ])>{{ __('View task') }}</p>
                                <x-mine.icon name="{{ $arrowIcon }}" size="16" weight="filled" />
                            </div>
                        </a>
                    </x-mine.animate>
                @else
                    <x-mine.animate wire:key="task-{{ $task->id }}" stagger="50" data-anim-index="{{ $loop->index }}"
                        class="min-w-50 mine-card mine-card-primary flex flex-col p-4">
                        <h2 class="text-sm font-semibold mine-text-primary mb-4">{{ $task->title }}</h2>
                        <div class="self-start mine-badge-primary rounded-xl h-7 flex items-center gap-1 px-2 mb-4">
                            <x-mine.icon name="Fire" size="16" weight="filled" />
                            <p class="font-medium text-xs">{{ $priorityLabel($task->priority->value) }}</p>
                        </div>
                        <div class="self-start mine-badge-primary rounded-xl h-7 flex items-center gap-1 px-2 mb-4">
                            <x-mine.icon name="Calendar" size="16" weight="filled" />
                            @php $daysUntil = -(int) $task->task_date->startOfDay()->diffInDays($today); @endphp
                            @if ($daysUntil === 0)
                                <p class="font-semibold text-xs">{{ __('Due today') }}</p>
                            @elseif ($daysUntil === 1)
                                <p class="font-semibold text-xs">{{ __('Due tomorrow') }}</p>
                            @else
                                <p class="font-semibold text-xs">{{ __('Due in') }}</p>
                                <p class="font-medium text-xs">{{ __(':count days', ['count' => $daysUntil]) }}</p>
                            @endif
                        </div>
                        <a wire:navigate href="{{ route('tasks', ['search' => $task->title]) }}"
                            class="relative inline-flex items-center justify-center h-9 px-4 rounded-xl text-sm font-semibold transition duration-200 ease-out cursor-pointer select-none whitespace-nowrap mine-btn-primary">
                            <div class="flex items-center gap-2">
                                <p @class([
                                    "pt-1" => app()->isLocale('en'),
                                    "text-[13px] font-medium"
                                ])>{{ __('View task') }}</p>
                                <x-mine.icon name="{{ $arrowIcon }}" size="16" weight="filled" />
                            </div>
                        </a>
                    </x-mine.animate>
                @endif
            @empty
                <x-mine.animate class="w-full mine-card flex items-center justify-center py-8 px-4 shadow-none">
                    <p class="text-sm font-medium mine-text-secondary">{{ __('No tasks need your attention right now.') }}</p>
                </x-mine.animate>
            @endforelse
        </x-mine.horizontal-scroll>
    </div>

    <div class="flex flex-col mb-6">
        <x-mine.animate delay="50" class="mb-4">
            <h1 class="font-semibold text-base mine-text-primary mb-1">{{ __("This week's workload") }}</h1>
            <p class="font-medium text-[13px] mine-text-secondary">{{ __('Estimated workload based on task duration.') }}</p>
        </x-mine.animate>
        <x-mine.horizontal-scroll :today-index="collect($this->week)->search(fn ($d) => $d['is_today'])">
            <div class="grid min-w-210 grid-cols-7 gap-1">
                @foreach ($this->week as $day)
                    @php $meta = $workloadMeta[$day['level']]; @endphp

                    <x-mine.animate data-day stagger="40" data-anim-index="{{ $loop->index }}"
                        class="{{ $day['is_today'] ? 'bg-(--mine-datepicker-day-selected-bg)' : '' }} min-h-30 mine-card shadow-none">
                        <div class="px-2 py-3 text-center">
                            <p
                                class="{{ $day['is_today'] ? 'font-semibold text-(--mine-datepicker-day-selected-text)' : 'mine-text-secondary' }} text-sm font-medium">
                                {{ $day['is_today'] ? __('Today') : $day['day'] }}
                            </p>
                            <p
                                class="{{ $day['is_today'] ? 'font-semibold text-(--mine-datepicker-day-selected-text)' : 'mine-text-secondary' }} mt-1 text-xs font-medium">
                                {{ $day['date'] }}
                            </p>
                        </div>
                        <x-mine.separator {{ $day['is_today'] ? 'class=border-white/30' : '' }} />
                        <div class="flex flex-col items-center justify-center px-2 pt-2 pb-4 text-center">
                            @if ($day['past'] || $day['minutes'] === 0)
                                <span
                                    class="{{ $day['is_today'] ? 'text-(--mine-datepicker-day-selected-text)' : 'mine-text-secondary' }} text-xl">—</span>
                            @else
                                <div class="flex gap-1 items-center">
                                    <p
                                        class="{{ $day['is_today'] ? 'text-(--mine-datepicker-day-selected-text)' : 'mine-text-primary' }} font-semibold text-sm">
                                        {{ $day['formatted'] }}
                                    </p>
                                </div>
                                <p
                                    class="{{ $day['is_today'] ? 'text-(--mine-datepicker-day-selected-text)' : $meta['text'] }} mb-1 mt-2 text-xs font-semibold">
                                    {{ $meta['label'] }}
                                </p>

                                <div class="mt-1.5 flex gap-1">
                                    @for ($i = 0; $i < 4; $i++)
                                        <span
                                            class="{{ $i < $meta['count'] ? $meta['dot'] : 'bg-(--mine-workload-empty-dot)' }} h-1.5 w-1.5 rounded-full"></span>
                                    @endfor
                                </div>
                            @endif
                        </div>
                    </x-mine.animate>
                @endforeach
            </div>
        </x-mine.horizontal-scroll>
    </div>

    <x-mine.animate delay="100" class="flex flex-col">
        <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-xs font-medium mine-text-secondary">
            @foreach (\App\Enums\WorkloadLevel::cases() as $level)
                <span class="inline-flex items-center gap-1.5">
                    <span class="{{ $workloadMeta[$level->value]['dot'] }} h-2 w-2 rounded-full"></span>
                    {{ $workloadMeta[$level->value]['range'] }}
                </span>
            @endforeach
        </div>
    </x-mine.animate>
</div>
