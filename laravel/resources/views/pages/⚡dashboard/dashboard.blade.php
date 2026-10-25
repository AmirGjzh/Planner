@php
    $today = now()->startOfDay();

    $digit = fn ($value) => app()->isLocale('en') ? $value : \App\Support\PersianNumber::convert($value);

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

<div class="flex max-w-7xl flex-1 flex-col px-4 pt-4 pb-4 sm:px-8 sm:pb-6 md:px-16 md:pb-14">
    <x-mine.animate class="mb-4 flex items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="mine-text-primary mb-1 text-base font-semibold">{{ __('Tasks needing attention') }}</h1>
                <div class="mine-btn-primary mb-1 flex size-6 items-center justify-center rounded-full pt-1 text-sm font-semibold">
                    {{ $digit(count($this->attentionTasks)) }}
                </div>
            </div>
            <p class="mine-text-secondary text-[13px] font-medium">
                {{ __('These tasks are due or their alarm time has been reached.') }}
            </p>
        </div>
        <a wire:navigate href="{{ route('tasks') }}">
            <x-mine.button type="button" height="h-10" size="sm" class="mine-btn-primary">
                <div class="flex items-center justify-center gap-2">
                    <span>
                        <span class="hidden md:inline">{{ __('View') }}</span> {{ __('All') }}
                        <span class="hidden sm:inline">{{ __('tasks') }}</span>
                    </span>
                    <x-mine.icon
                        :name="$arrowIcon"
                        weight="filled"
                        size="16"
                        @class(['mb-1' => app()->isLocale('en')])
                    />
                </div>
            </x-mine.button>
        </a>
    </x-mine.animate>
    <x-mine.horizontal-scroll scroller-class="flex gap-2 self-center pb-10" class="px-0!">
        @forelse ($this->attentionTasks as $task)
            @if ($task->task_date < $today)
                <x-mine.animate
                    wire:key="task-{{ $task->id }}"
                    stagger="50"
                    data-anim-index="{{ $loop->index }}"
                    class="mine-card mine-card-danger flex max-w-70 min-w-70 flex-col justify-between gap-4 p-4"
                >
                    <div class="flex">
                        <div @class([
                            'mr-4' => app()->isLocale('en'),
                            'ml-4' => app()->isLocale('fa'),
                            'shrink-0 mine-badge-danger rounded-lg size-16 flex justify-center items-center',
                        ])>
                            <x-mine.icon name="ClipboardRemove" weight="filled" size="32" />
                        </div>
                        <div class="flex min-w-0 flex-col justify-between gap-2 py-1">
                            <h2 @class([
                                'pt-1' => app()->isLocale('en'),
                                'text-[15px] font-semibold mine-text-primary truncate min-w-0',
                            ])>
                                {{ $task->title }}
                            </h2>
                            <p class="mine-text-secondary line-clamp-2 min-w-0 text-[13px] font-medium">
                                {{ $task->description ?: __('No description') }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <div @class(['pl-3 pr-4' => app()->isLocale('en'), 'pr-3 pl-4' => app()->isLocale('fa'), 'mine-badge-danger mb-4 flex h-8 items-center gap-2 self-start rounded-lg'])>
                                <x-mine.icon name="Fire" size="16" weight="filled" />
                                <p @class(['pt-1' => app()->isLocale('en'), 'text-xs font-medium'])>
                                    {{ $priorityLabel($task->priority->value) }}
                                </p>
                            </div>
                            <div @class(['pl-3 pr-4' => app()->isLocale('en'), 'pr-3 pl-4' => app()->isLocale('fa'), 'mine-badge-danger mb-4 flex h-8 items-center gap-2 self-start rounded-lg'])>
                                <x-mine.icon name="Calendar" size="16" weight="filled" />
                                @php $daysAgo = (int) $task->task_date->startOfDay()->diffInDays($today); @endphp
                                <p @class(['pt-1' => app()->isLocale('en'), 'text-xs font-medium'])>
                                    {{ $daysAgo === 1 ? __('1 day ago') : __(':count days ago', ['count' => $digit($daysAgo)]) }}
                                </p>
                            </div>
                        </div>
                        <a wire:navigate href="{{ route('tasks', ['search' => $task->title]) }}" class="w-full">
                            <x-mine.button type="button" height="h-9" size="sm" class="mine-btn-danger">
                                <div class="flex items-center justify-center gap-2">
                                    <span>{{ __('View task') }}</span>
                                    <x-mine.icon
                                        :name="$arrowIcon"
                                        weight="filled"
                                        size="16"
                                        @class(['mb-1' => app()->isLocale('en')])
                                    />
                                </div>
                            </x-mine.button>
                        </a>
                    </div>
                </x-mine.animate>
            @else
                <x-mine.animate
                    wire:key="task-{{ $task->id }}"
                    stagger="50"
                    data-anim-index="{{ $loop->index }}"
                    class="mine-card mine-card-primary flex max-w-70 min-w-70 flex-col justify-between gap-4 p-4"
                >
                    <div class="flex">
                        <div @class([
                            'mr-4' => app()->isLocale('en'),
                            'ml-4' => app()->isLocale('fa'),
                            'shrink-0 mine-badge-primary rounded-lg size-16 flex justify-center items-center',
                        ])>
                            <x-mine.icon name="Clipboard" weight="filled" size="32" />
                        </div>
                        <div class="flex min-w-0 flex-col justify-between gap-2 py-1">
                            <h2 @class([
                                'pt-1' => app()->isLocale('en'),
                                'text-[15px] font-semibold mine-text-primary truncate min-w-0',
                            ])>
                                {{ $task->title }}
                            </h2>
                            <p class="mine-text-secondary line-clamp-2 min-w-0 text-[13px] font-medium">
                                {{ $task->description ?: __('No description') }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <div @class(['pl-3 pr-4' => app()->isLocale('en'), 'pr-3 pl-4' => app()->isLocale('fa'), 'mine-badge-primary mb-4 flex h-8 items-center gap-2 self-start rounded-lg'])>
                                <x-mine.icon name="Fire" size="16" weight="filled" />
                                <p @class(['pt-1' => app()->isLocale('en'), 'text-xs font-medium'])>
                                    {{ $priorityLabel($task->priority->value) }}
                                </p>
                            </div>
                            <div @class(['pl-3 pr-4' => app()->isLocale('en'), 'pr-3 pl-4' => app()->isLocale('fa'), 'mine-badge-primary mb-4 flex h-8 items-center gap-2 self-start rounded-lg'])>
                                <x-mine.icon name="Calendar" size="16" weight="filled" />
                                @php $daysUntil = -(int) $task->task_date->startOfDay()->diffInDays($today); @endphp
                                <p @class(['pt-1' => app()->isLocale('en'), 'text-xs font-medium'])>
                                    {{
                                        match (true) {
                                            $daysUntil === 0 => __('Due today'),
                                            $daysUntil === 1 => __('Due tomorrow'),
                                            default => __('in :count days', ['count' => $digit($daysUntil)]),
                                        }
                                    }}
                                </p>
                            </div>
                        </div>
                        <a wire:navigate href="{{ route('tasks', ['search' => $task->title]) }}" class="w-full">
                            <x-mine.button type="button" height="h-9" size="sm" class="mine-btn-primary">
                                <div class="flex items-center justify-center gap-2">
                                    <span>{{ __('View task') }}</span>
                                    <x-mine.icon
                                        :name="$arrowIcon"
                                        weight="filled"
                                        size="16"
                                        @class(['mb-1' => app()->isLocale('en')])
                                    />
                                </div>
                            </x-mine.button>
                        </a>
                    </div>
                </x-mine.animate>
            @endif
        @empty
            <x-mine.animate class="mine-card flex w-full flex-col items-center justify-center px-4 py-8 shadow-none">
                <x-mine.icon name="Clipboard" weight="filled" size="40" class="mine-text-secondary mx-auto mb-3" />
                <p class="mine-text-secondary text-sm font-medium">
                    {{ __('No tasks need your attention right now.') }}
                </p>
            </x-mine.animate>
        @endforelse
    </x-mine.horizontal-scroll>

    <div class="mb-6 flex flex-col">
        <x-mine.animate delay="50" class="mb-4">
            <h2 class="mine-text-primary mb-1 text-base font-semibold">{{ __("This week's workload") }}</h2>
            <p class="mine-text-secondary text-[13px] font-medium">
                {{ __('Estimated workload based on task duration.') }}
            </p>
        </x-mine.animate>
        <x-mine.horizontal-scroll :today-index="collect($this->week)->search(fn ($d) => $d['is_today'])">
            <div class="grid min-w-240 grid-cols-7 gap-1">
                @foreach ($this->week as $day)
                    @php $meta = $workloadMeta[$day['level']]; @endphp

                    <x-mine.animate
                        data-day
                        stagger="40"
                        data-anim-index="{{ $loop->index }}"
                        class="{{ $day['is_today'] ? 'bg-(--mine-datepicker-day-selected-bg)' : '' }} min-h-30 mine-card shadow-none"
                    >
                        <div class="px-2 py-3 text-center">
                            <p class="{{ $day['is_today'] ? 'font-semibold text-(--mine-datepicker-day-selected-text)' : 'mine-text-secondary' }} text-sm font-medium">
                                {{ $day['is_today'] ? __('Today') : $day['day'] }}
                            </p>
                            <p class="{{ $day['is_today'] ? 'font-semibold text-(--mine-datepicker-day-selected-text)' : 'mine-text-secondary' }} mt-1 text-xs font-medium">
                                {{ $day['date'] }}
                            </p>
                        </div>
                        <div class="opacity-40">
                            <x-mine.separator :class="$day['is_today'] ? 'bg-white/30!' : ''" />
                        </div>
                        <div class="flex flex-col items-center justify-center px-2 pt-3 pb-4 text-center">
                            @if ($day['past'] || $day['minutes'] === 0)
                                <span class="{{ $day['is_today'] ? 'text-(--mine-datepicker-day-selected-text)' : 'mine-text-secondary' }} text-xl">—</span>
                            @else
                                <p class="{{ $day['is_today'] ? 'text-(--mine-datepicker-day-selected-text)' : 'mine-text-primary' }} font-semibold text-xs">
                                    {{ $day['formatted'] }}
                                </p>
                                <p class="{{ $day['is_today'] ? 'text-(--mine-datepicker-day-selected-text)' : $meta['text'] }} mb-1 mt-2 text-xs font-semibold">
                                    {{ $meta['label'] }}
                                </p>

                                <div class="mt-1.5 flex gap-1">
                                    @for ($i = 0; $i < 4; $i++)
                                        <span class="{{ $i < $meta['count'] ? $meta['dot'] : 'bg-(--mine-workload-empty-dot)' }} h-1.5 w-1.5 rounded-full"></span>
                                    @endfor
                                </div>
                            @endif
                        </div>
                    </x-mine.animate>
                @endforeach
            </div>
        </x-mine.horizontal-scroll>
    </div>

    <x-mine.animate
        delay="100"
        class="mine-text-secondary flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-xs font-medium"
    >
        @foreach (\App\Enums\WorkloadLevel::cases() as $level)
            <span class="inline-flex items-center gap-1.5">
                <span class="{{ $workloadMeta[$level->value]['dot'] }} h-2 w-2 rounded-full"></span>
                {{ $workloadMeta[$level->value]['range'] }}
            </span>
        @endforeach
    </x-mine.animate>
</div>
