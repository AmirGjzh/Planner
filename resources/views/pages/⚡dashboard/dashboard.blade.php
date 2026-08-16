@php
    $today = now()->startOfDay();

    $workloadMeta = collect(\App\Enums\WorkloadLevel::cases())->mapWithKeys(fn ($level) => [
        $level->value => [
            'label' => $level->label(),
            'dot' => 'bg-(--mine-workload-'.$level->value.'-dot)',
            'text' => 'text-(--mine-workload-'.$level->value.'-text)',
            'count' => match ($level) {
                \App\Enums\WorkloadLevel::Light => 1,
                \App\Enums\WorkloadLevel::Moderate => 2,
                \App\Enums\WorkloadLevel::Heavy => 3,
                \App\Enums\WorkloadLevel::VeryHeavy => 4,
                default => 0,
            },
        ],
    ])->all();
@endphp

<div class="flex-1 px-4 sm:px-8 md:px-16 pb-6 pt-4 flex flex-col">
    <div class="flex flex-col mb-6">
        <div class="flex items-center justify-between gap-4 mb-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="font-bold text-md mine-text-primary mb-1">Tasks needing attention</h1>
                    <div class="rounded-full size-6 flex items-center justify-center mine-badge-danger text-xs font-bold">
                        {{ count($this->upcomingTasks) }}
                    </div>
                </div>
                <p class="font-medium text-sm mine-text-secondary">These tasks are due or their alarm time has been
                    reached.</p>
            </div>
            <a wire:navigate href="{{ route('tasks') }}"
                class="relative inline-flex items-center justify-center h-10 px-4 rounded-xl text-[15px] font-semibold transition duration-200 ease-out cursor-pointer select-none whitespace-nowrap mine-btn-outline-primary">
                <div class="flex items-center gap-1">
                    <p class="text-sm font-medium"><span class="hidden md:inline">View</span> All <span
                            class="hidden sm:inline">tasks</span></p>
                    <x-mine.icon name="arrow-long-right" variant="micro" class="size-4" />
                </div>
            </a>
        </div>
        <x-mine.horizontal-scroll scroller-class="flex gap-2 self-center" class="px-0!">
            @forelse ($this->upcomingTasks as $task)
                @if ($task->task_date < $today)
                    <div wire:key="task-{{ $task->id }}"
                        class="min-w-50 mine-card border-(--mine-alert-danger-border) border-2 flex flex-col py-4 px-4 shadow-none">
                        <h2 class="text-sm font-bold mine-text-primary mb-4">{{ $task->title }}</h2>
                        <div class="self-start mine-badge-danger rounded-lg h-6 flex items-center gap-1 px-2 mb-4">
                            <x-mine.icon name="fire" class="size-4" variant="micro" />
                            <p class="font-bold text-xs">{{ $task->priority }}</p>
                        </div>
                        <div class="self-start mine-badge-danger rounded-lg h-6 flex items-center gap-1 px-2 mb-4">
                            <x-mine.icon name="calendar" class="size-5" variant="micro" />
                            <p class="font-bold text-xs">Overdue</p>
                            @php $daysAgo = (int) $task->task_date->startOfDay()->diffInDays($today); @endphp
                            <p class="font-medium text-xs">{{ $daysAgo === 1 ? '1 day ago' : $daysAgo.' days ago' }}</p>
                        </div>
                        <a wire:navigate href="{{ route('tasks', ['search' => $task->title]) }}"
                            class="relative inline-flex items-center justify-center h-10 px-4 rounded-xl text-[15px] font-semibold transition duration-200 ease-out cursor-pointer select-none whitespace-nowrap mine-btn-outline-danger">
                            <div class="flex items-center gap-1">
                                <p class="text-sm font-medium">View task</p>
                                <x-mine.icon name="arrow-long-right" variant="micro" class="size-4" />
                            </div>
                        </a>
                    </div>
                @else
                    <div wire:key="task-{{ $task->id }}"
                        class="min-w-50 mine-card border-(--mine-alert-success-border) border-2 flex flex-col py-4 px-4 shadow-none">
                        <h2 class="text-sm font-bold mine-text-primary mb-4">{{ $task->title }}</h2>
                        <div class="self-start mine-badge-primary rounded-lg h-6 flex items-center gap-1 px-2 mb-4">
                            <x-mine.icon name="fire" class="size-4" variant="micro" />
                            <p class="font-bold text-xs">{{ $task->priority }}</p>
                        </div>
                        <div class="self-start mine-badge-primary rounded-lg h-6 flex items-center gap-1 px-2 mb-4">
                            <x-mine.icon name="calendar" class="size-5" variant="micro" />
                            @php $daysUntil = -(int) $task->task_date->startOfDay()->diffInDays($today); @endphp
                            @if ($daysUntil === 0)
                                <p class="font-bold text-xs">Due today</p>
                            @elseif ($daysUntil === 1)
                                <p class="font-bold text-xs">Due tomorrow</p>
                            @else
                                <p class="font-bold text-xs">Due in</p>
                                <p class="font-medium text-xs">{{ $daysUntil }} days</p>
                            @endif
                        </div>
                        <a wire:navigate href="{{ route('tasks', ['search' => $task->title]) }}"
                            class="relative inline-flex items-center justify-center h-10 px-4 rounded-xl text-[15px] font-semibold transition duration-200 ease-out cursor-pointer select-none whitespace-nowrap mine-btn-outline-primary">
                            <div class="flex items-center gap-1">
                                <p class="text-sm font-medium">View task</p>
                                <x-mine.icon name="arrow-long-right" variant="micro" class="size-4" />
                            </div>
                        </a>
                    </div>
                @endif
            @empty
                <div class="w-full mine-card flex items-center justify-center py-8 px-4 shadow-none">
                    <p class="text-sm font-medium mine-text-secondary">No tasks need your attention right now.</p>
                </div>
            @endforelse
        </x-mine.horizontal-scroll>
    </div>

    <div class="flex flex-col mb-6">
        <div class="mb-4">
            <h1 class="font-bold text-md mine-text-primary mb-1">This week's workload</h1>
            <p class="font-medium text-sm mine-text-secondary">Estimated workload based on task duration.</p>
        </div>
        <x-mine.horizontal-scroll :today-index="collect($this->week)->search(fn ($d) => $d['is_today'])">
            <div class="grid min-w-210 grid-cols-7 gap-1">
                @foreach ($this->week as $day)
                    @php $meta = $workloadMeta[$day['level']]; @endphp

                    <div data-day
                        class="{{ $day['is_today'] ? 'bg-(--mine-alert-success-bg) border-2 border-(--mine-alert-success-icon)' : '' }} min-h-30 mine-card shadow-none">
                        <div class="px-2 py-3 text-center">
                            <p
                                class="{{ $day['is_today'] ? 'font-semibold text-(--mine-alert-success-title)' : 'mine-text-secondary' }} text-sm font-medium">
                                {{ $day['is_today'] ? 'Today' : $day['day'] }}
                            </p>
                            <p
                                class="{{ $day['is_today'] ? 'font-semibold text-(--mine-alert-success-title)' : 'mine-text-secondary' }} mt-0.5 text-xs font-medium">
                                {{ $day['date'] }}
                            </p>
                        </div>
                        <x-mine.separator />
                        <div class="flex flex-col items-center justify-center px-2 pt-2 pb-4 text-center">
                            @if ($day['past'] || $day['minutes'] === 0)
                                <span class="text-lg mine-text-secondary">—</span>
                            @else
                                <div class="flex gap-1 items-center">
                                    <p class="font-bold mine-text-primary">{{ $day['formatted'] }}</p>
                                </div>
                                <p class="{{ $meta['text'] }} mt-2 text-xs font-semibold">{{ $meta['label'] }}</p>

                                <div class="mt-1.5 flex gap-1">
                                    @for ($i = 0; $i < 4; $i++)
                                        <span
                                            class="{{ $i < $meta['count'] ? $meta['dot'] : 'bg-(--mine-workload-empty-dot)' }} h-1.5 w-1.5 rounded-full"></span>
                                    @endfor
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </x-mine.horizontal-scroll>
    </div>

    <div class="flex flex-col">
        <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-xs font-medium mine-text-secondary">
            @foreach (\App\Enums\WorkloadLevel::cases() as $level)
                <span class="inline-flex items-center gap-1.5">
                    <span class="{{ $workloadMeta[$level->value]['dot'] }} h-2 w-2 rounded-full"></span>
                    {{ $level->rangeLabel() }}
                </span>
            @endforeach
        </div>
    </div>
</div>
